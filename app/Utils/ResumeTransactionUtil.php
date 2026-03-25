<?php

namespace App\Utils;

use App\Models\Certificate;
use App\Models\Experience;
use App\Models\Job;
use Illuminate\Support\Facades\Http;
use App\Models\ParsedData;
use App\Models\Strength;
use App\Models\Weakness;
use App\Models\Skill;
use App\Models\SoftSkill;
use App\Models\JobRecommendation;
use App\Models\User;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class ResumeTransactionUtil
{
    public function callAISummarizePdf($file)
    {
        $response = Http::timeout(120)
            ->attach(
                'file',
                file_get_contents($file),
                $file->getClientOriginalName()
            )
            ->post('http://204.236.202.130:5000/analyze');

        if ($response->successful()) {
            $result = $response->json();

            $relevantLabels = [
                'technical_skills' => $result['technical_skills'] ?? [],
                'soft_skills' => $result['soft_skills'] ?? [],
                'strengths' => $result['strengths'] ?? [],
                'weaknesses' => $result['weaknesses'] ?? [],
                'summary' => $result['summary'] ?? '',
                'certificates' => $result['certificates'] ?? [],
            ];

            return $relevantLabels;
        }

        return [
            'technical_skills' => [],
            'soft_skills' => [],
            'strengths' => [],
            'weaknesses' => [],
            'summary' => '',
            'certificates' => [],
        ];
    }

    public function newStoreDataSummarizeData($aiResult, $resume)
    {
        if (!$aiResult || !is_array($aiResult)) {
            \Log::error('Gemini AI Result is empty or invalid JSON');
            return null;
        }

        $parsedData = ParsedData::create([
            'resume_id' => $resume->id,
            'summary_text' => $aiResult['summary'] ?? '',
        ]);

        $dataMap = [
            'strengths'        => Strength::class,
            'weaknesses'       => Weakness::class,
            'technical_skills' => Skill::class,
            'certifications'   => Certificate::class,
            'experiences'      => Experience::class,
            'soft_skills'      => SoftSkill::class,
        ];

        foreach ($dataMap as $key => $modelClass) {
            if (isset($aiResult[$key]) && is_array($aiResult[$key]) && count($aiResult[$key]) > 0) {

                $records = [];
                foreach ($aiResult[$key] as $value) {
                    if (!empty($value)) {
                        $records[] = [
                            'parsed_data_id' => $parsedData->id,
                            'description'    => is_array($value) ? json_encode($value) : $value,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }
                }

                if (!empty($records)) {
                    $modelClass::insert($records);
                }
            }
        }

        return $parsedData;
    }

    public function extractTedx($request)
    {
        $file = $request->file('pdf');
        $extension = $file->getClientOriginalExtension();
        $text = '';

        if (in_array($extension, ['pdf'])) {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();
        } elseif (in_array($extension, ['doc', 'docx'])) {
            $phpWord = IOFactory::load($file->getRealPath());
            $text = '';
            foreach ($phpWord->getSections() as $section) {
                $elements = $section->getElements();
                foreach ($elements as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unsupported file type'
            ], 400);
        }
        return $text;
    }

    public function findBestMatch($ai_result, $endpoint, $parsed_data_id = null)
    {
        $jobs_data = Job::select('id', 'title', 'company_name', 'skills', 'experience_level', 'education_certificate', 'link')->limit(100)->get();

        $prompt = "Compare this CV data: " . json_encode($ai_result) .
            " with this Jobs List: " . json_encode($jobs_data) .
            ". Identify the top 3 best matching jobs. Return ONLY a VALID JSON array of objects, where each object contains:
                'job_id',
                'final_score',
                'matched_skills' (This must be a JSON array of strings, e.g., [\"PHP\", \"Laravel\"]),
                'company_name',
                'title',
                'link'.
                The response must be a plain JSON array.";

        $response = Http::timeout(120)->withHeaders([
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ]);

        if ($response->successful()) {
            $responseText = $response->json()['candidates'][0]['content']['parts'][0]['text'];
            $result = json_decode(trim($responseText), true);

            if ($parsed_data_id && is_array($result)) {
                foreach ($result as $rec) {
                    JobRecommendation::updateOrCreate(
                        [
                            'parsed_data_id' => $parsed_data_id,
                            'job_id' => $rec['job_id'] ?? null,
                        ],
                        [
                            'job_title' => $rec['job_title'] ?? '',
                            'company_name' => $rec['company_name'] ?? '',
                            'match_score' => (int)str_replace('%', '', $rec['final_score'] ?? '0'),
                            'matched_skills' => isset($rec['matched_skills']) ? json_encode($rec['matched_skills']) : null,
                            'link' => $rec['link'] ?? null,
                        ]
                    );
                }
            }

            return $result;
        }

        return [];
    }

    public function getRecommendationsJobs($user_id, $resume = null, $parsed_data = null)
    {
        if (!$parsed_data) {
            $user = User::with([
                'cv_lists' => function ($query) {
                    $query->latest();
                },
                'cv_lists.parsedData' => function ($query) {
                    $query->with([
                        'strengths',
                        'weaknesses',
                        'technical_skills',
                        'soft_skills',
                        'certificates',
                        'experiences'
                    ]);
                }
            ])->findOrFail($user_id);

            $latest_resume = $user->cv_lists->where('id', $resume->id)->first();

            if (!$latest_resume || !$latest_resume->parsedData) {
                return [];
            }

            $parsed_data = $latest_resume->parsedData;
        }

        $user_profile = implode(" ", [
            $parsed_data->summary_text,
            implode(" ", $parsed_data->strengths->pluck('description')->toArray()),
            implode(" ", $parsed_data->technical_skills->pluck('description')->toArray()),
            implode(" ", $parsed_data->soft_skills->pluck('description')->toArray()),
            implode(" ", $parsed_data->certificates->pluck('description')->toArray()),
            implode(" ", $parsed_data->experiences->pluck('description')->toArray()),
        ]);

        // ml model data
        $response = Http::post('http://34.207.207.47:5000/recommend', [
            'skills' =>
            $parsed_data->technical_skills->pluck('description')->implode(', ') . ', ' .
                $parsed_data->soft_skills->pluck('description')->implode(', ') . ', ' .
                $parsed_data->strengths->pluck('description')->implode(', '),
            'experience' => $parsed_data->experiences->pluck('description')->implode(', ') . $parsed_data->summary_text,
            'certificates' => $parsed_data->certificates->pluck('description')->implode(', '),
        ]);

        if ($response->failed()) {
            return [];
        }

        // if (is_array($response->json())) {
        //     foreach ($response->json() as $rec) {
        //         JobRecommendation::updateOrCreate(
        //             [
        //                 'parsed_data_id' => $parsed_data->id,
        //                 'job_id' => $rec['id'] ?? null,
        //             ],
        //             [
        //                 'job_title' => $rec['title'] ?? '',
        //                 'company_name' => $rec['company_name'] ?? '',
        //                 'match_score' => $rec['final_score'] ?? 0,
        //                 'matched_skills' => isset($rec['matched_skills']) ? json_encode($rec['matched_skills']) : null,
        //                 'link' => isset($rec['link']) ? $rec['link'] : null,
        //             ]
        //         );
        //     }
        // }

        return $response->json();
    }

    public function getProfileData($user_id)
    {
        $user = User::with(['cv_lists' => function ($query) {
            $query->latest();
        }, 'cv_lists.parsedData' => function ($query) {
            $query->with(['strengths', 'weaknesses', 'technical_skills', 'soft_skills', 'certificates', 'experiences', 'job_recommendations']);
        }])->findOrFail($user_id);

        if ($user->cv_lists->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No resumes found for this user'
            ], 404);
        }

        $allCvData = $user->cv_lists->map(function ($resume) {
            $parsedData = $resume->parsedData;

            if (!$parsedData) {
                return null;
            }

            return [
                'cv_url' => asset($resume->file_path),
                'strengths' => $parsedData->strengths->pluck('description'),
                'weaknesses' => $parsedData->weaknesses->pluck('description'),
                'skills' => $parsedData->technical_skills->pluck('description'),
                'soft_skills' => $parsedData->soft_skills->pluck('description'),
                'certificates' => $parsedData->certificates->pluck('description'),
                'experiences' => $parsedData->experiences->pluck('description'),
                'summary' => $parsedData->summary_text,
                'job_recommendations' => $parsedData->job_recommendations->map(function ($item) {
                    return [
                        'job_title' => $item->job_title,
                        'match_score' => $item->match_score,
                        'company_name' => $item->company_name,
                        'matched_skills' => isset($item->matched_skills) ? json_encode($item->matched_skills) : null,
                        'link' => $item->link ?? null
                    ];
                }),
            ];
        })->filter()->values();

        if ($allCvData->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No parsed resumes found for this user'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $allCvData
        ]);
    }

    //ollam server request
    public function ollamRead($text)
    {
        $base64Text = base64_encode($text);

        $model = 'llama3.2:1b';

        $prompt = "
                You are a professional CV analyzer. Analyze the following CV text and return a VALID JSON object with fields:
                - experiences (array of strings)
                - soft_skills (array of strings)
                - technical_skills (array of strings)
                - weaknesses (array of strings)
                - summary (short paragraph)
                - strengths (array of strings)
                - certifications (array of strings)

                CV Text:
                \"\"\"{$base64Text}\"\"\"
                ";

        $response = Http::timeout(180)->post(
            'http://44.220.55.233:11434/api/generate',
            [
                'model' => 'llama3.2:1b',
                'prompt' => $prompt,
                'stream' => false,
                'format' => 'json'
            ]
        );

        if ($response->successful()) {
            $data = $response->json();
            dd($data);
            return $data;
        } else {
            return $response->body();
        }
    }
}
