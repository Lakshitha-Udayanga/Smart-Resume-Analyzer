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
            'strengths' => Strength::class,
            'weaknesses' => Weakness::class,
            'technical_skills' => Skill::class,
            'certifications' => Certificate::class,
            'experiences' => Experience::class,
            'soft_skills' => SoftSkill::class,
        ];

        foreach ($dataMap as $key => $modelClass) {
            if (isset($aiResult[$key]) && is_array($aiResult[$key]) && count($aiResult[$key]) > 0) {

                $records = [];
                foreach ($aiResult[$key] as $value) {
                    if (!empty($value)) {
                        $records[] = [
                            'parsed_data_id' => $parsedData->id,
                            'description' => is_array($value) ? json_encode($value) : $value,
                            'created_at' => now(),
                            'updated_at' => now(),
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
                            'job_title' => $rec['job_title'] ?? ($rec['title'] ?? ''),
                        ],
                        [
                            'match_percentage' => (float) ($rec['final_score'] ?? ($rec['score'] ?? ($rec['match_percentage'] ?? 0))),
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
        $response = Http::post('http://52.90.229.61:5000/predict', [
            'skills' =>
                $parsed_data->technical_skills->pluck('description')->implode(', ') . ', ' .
                $parsed_data->soft_skills->pluck('description')->implode(', ') . ', ' .
                $parsed_data->strengths->pluck('description')->implode(', '),
            'experience' => $parsed_data->experiences->pluck('description')->implode(', ') . $parsed_data->summary_text,
            'certificates' => $parsed_data->certificates->pluck('description')->implode(', '),
        ]);

        if ($response->successful()) {
            $this->storeRecommendations($response->json(), $parsed_data->id);
        }

        if ($response->failed()) {
            return [];
        }
        return $response->json();
    }

    public function getJobsList($job_recommendations)
    {
        $title = null;
        if (isset($job_recommendations['best_match']['job_title'])) {
            $title = $job_recommendations['best_match']['job_title'];
        } elseif (isset($job_recommendations[0]['job_title'])) {
            $title = $job_recommendations[0]['job_title'];
        } elseif (isset($job_recommendations[0]['title'])) {
            $title = $job_recommendations[0]['title'];
        }

        return $this->fetchJobsByLevel($title);
    }

    public function getProfileData($user_id)
    {
        $user = User::with([
            'cv_lists' => function ($query) {
                $query->latest();
            },
            'cv_lists.parsedData' => function ($query) {
                $query->with(['strengths', 'weaknesses', 'technical_skills', 'soft_skills', 'certificates', 'experiences', 'job_recommendations.jobs']);
            }
        ])->findOrFail($user_id);

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
                        'match_percentage' => $item->match_percentage,
                        'matched_jobs' => $this->fetchJobsByLevel($item->job_title)
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

    public function prepareDataSet($parsed_data, $jobs_list)
    {
        $cv_data = [
            'experiences' => $parsed_data->experiences->pluck('description')->toArray(),
            'certificates' => $parsed_data->certificates->pluck('description')->toArray(),
            'soft_skills' => $parsed_data->soft_skills->pluck('description')->toArray(),
            'technical_skills' => $parsed_data->technical_skills->pluck('description')->toArray(),
            'strengths' => $parsed_data->strengths->pluck('description')->toArray(),
        ];

        $jobs_data = $jobs_list->map(function ($job) {
            return [
                'title' => $job->title,
                'company_name' => $job->company_name,
                'location' => $job->location,
                'experience_level' => $job->experience_level,
                'skills' => $job->skills,
                'education_certificate' => $job->education_certificate,
                'link' => $job->link,
            ];
        })->toArray();

        return [
            'cv_data' => json_encode($cv_data),
            'jobs_data' => json_encode($jobs_data),
        ];
    }

    public function storeRecommendations($response, $parsed_data_id)
    {
        if (empty($response)) {
            return;
        }

        $jobData = [];

        if (isset($response['best_match']['job_title'])) {
            $jobData[] = [
                'title' => $response['best_match']['job_title'],
                'percentage' => $response['best_match']['score'] ?? ($response['best_match']['match_percentage'] ?? null)
            ];
        }

        if (isset($response['recommendations']) && is_array($response['recommendations'])) {
            foreach ($response['recommendations'] as $rec) {
                if (is_array($rec)) {
                    $jobData[] = [
                        'title' => $rec['job_title'] ?? null,
                        'percentage' => $rec['score'] ?? ($rec['match_percentage'] ?? null)
                    ];
                } else {
                    $jobData[] = [
                        'title' => $rec,
                        'percentage' => null
                    ];
                }
            }
        }

        if (empty($jobData) && is_array($response)) {
            foreach ($response as $item) {
                if (is_array($item)) {
                    $jobData[] = [
                        'title' => $item['job_title'] ?? ($item['title'] ?? null),
                        'percentage' => $item['final_score'] ?? ($item['score'] ?? ($item['match_percentage'] ?? null))
                    ];
                } elseif (is_string($item)) {
                    $jobData[] = ['title' => $item, 'percentage' => null];
                }
            }
        }

        if ($parsed_data_id) {
            $seenTitles = [];
            foreach ($jobData as $data) {
                if ($data['title'] && !in_array($data['title'], $seenTitles)) {
                    JobRecommendation::create([
                        'parsed_data_id' => $parsed_data_id,
                        'job_title' => $data['title'],
                        'match_percentage' => (float)($data['percentage'] ?? 0)
                    ]);
                    $seenTitles[] = $data['title'];
                }
            }
        }
    }

    // public function getClientProfile($user_id)
    // {
    //     $user = User::with([
    //         'cv_lists' => function ($query) {
    //             $query->latest()->limit(1);
    //         },
    //         'cv_lists.parsedData' => function ($query) {
    //             $query->with([
    //                 'strengths',
    //                 'weaknesses',
    //                 'technical_skills',
    //                 'soft_skills',
    //                 'certificates',
    //                 'experiences',
    //                 'job_recommendations.jobs'
    //             ]);
    //         }
    //     ])->findOrFail($user_id);

    //     $resume = $user->cv_lists->first();

    //     if (!$resume || !$resume->parsedData) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'No resume data found'
    //         ];
    //     }

    //     $parsedData = $resume->parsedData;

    //     return [
    //         'user' => [
    //             'name' => $user->name,
    //             'email' => $user->email,
    //         ],
    //         'resume_details' => [
    //             'cv_url' => asset($resume->file_path),
    //             'summary' => $parsedData->summary_text,
    //             'strengths' => $parsedData->strengths->pluck('description'),
    //             'weaknesses' => $parsedData->weaknesses->pluck('description'),
    //             'technical_skills' => $parsedData->technical_skills->pluck('description'),
    //             'soft_skills' => $parsedData->soft_skills->pluck('description'),
    //             'certificates' => $parsedData->certificates->pluck('description'),
    //             'experiences' => $parsedData->experiences->pluck('description'),
    //         ],
    //         'recommendations' => $parsedData->job_recommendations->map(function ($item) {
    //             return [
    //                 'recommended_title' => $item->job_title,
    //                 'top_jobs' => $this->fetchJobsByLevel($item->job_title)
    //             ];
    //         })
    //     ];
    // }

    public function fetchJobsByLevel($title)
    {
        if (!$title) {
            return collect([]);
        }

        $levels = [
            'intern' => ['Intern', 'Trainee'],
            'junior' => ['Junior', 'Associate', 'Entry Level'],
            'mid'    => ['Mid', 'Mid Level', 'Intermediate'],
            'senior' => ['Senior', 'Sr.'],
            'lead'   => ['Lead', 'Manager', 'Architect', 'Principal', 'Head'],
        ];

        $allJobs = collect();

        foreach ($levels as $key => $values) {
            $jobs = Job::where('title', 'LIKE', "%{$title}%")
                ->where(function ($query) use ($values) {
                    foreach ($values as $value) {
                        $query->orWhere('experience_level', 'LIKE', "%{$value}%");
                    }
                })
                ->orderBy('salary_min', 'desc')
                ->limit(3)
                ->get();

            $allJobs = $allJobs->merge($jobs);
        }

        return $allJobs->sortByDesc('salary_min')->values();
    }
}
