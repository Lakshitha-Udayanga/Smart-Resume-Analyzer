<?php

namespace App\Utils;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;
use App\Models\ParsedData;
use App\Models\Strength;
use App\Models\Weakness;
use App\Models\Skill;

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

    public function storeSummarizeData($aiResult, $resume)
    {
        // Save parsed summary
        $parsedData = ParsedData::create([
            'resume_id' => $resume->id,
            'summary_text' => $aiResult['summary'] ?? '',
        ]);

        $dataMap = [
            'strengths' => Strength::class,
            'weaknesses' => Weakness::class,
            'technical_skills' => Skill::class,
            'certificates' => Certificate::class,
        ];

        foreach ($dataMap as $key => $model) {

            if (!empty($aiResult[$key])) {

                $records = array_map(function ($value) use ($parsedData) {
                    return [
                        'parsed_data_id' => $parsedData->id,
                        'description' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }, $aiResult[$key]);

                $model::insert($records);
            }
        }

        return $parsedData;
    }
}
