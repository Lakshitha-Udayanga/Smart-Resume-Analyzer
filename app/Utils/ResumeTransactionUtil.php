<?php

namespace App\Utils;

use App\Models\Certificate;
use App\Models\Experience;
use Illuminate\Support\Facades\Http;
use App\Models\ParsedData;
use App\Models\Strength;
use App\Models\Weakness;
use App\Models\Skill;
use App\Models\SoftSkill;
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
}
