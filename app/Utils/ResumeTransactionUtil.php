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
    // Your methods here

    public function callAISummarizePdf($file)
    {
        $response = Http::timeout(120)
            ->attach(
                'file',
                file_get_contents($file),
                $file->getClientOriginalName()
            )
            ->post('http://54.205.147.241:5000/analyze');

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

        if (!empty($aiResult['strengths'])) {
            $strengths = array_map(fn($desc) => [
                'parsed_data_id' => $parsedData->id,
                'description' => $desc,
                'created_at' => now(),
                'updated_at' => now(),
            ], $aiResult['strengths']);

            Strength::insert($strengths);
        }

        if (!empty($aiResult['weaknesses'])) {
            $weaknesses = array_map(fn($desc) => [
                'parsed_data_id' => $parsedData->id,
                'description' => $desc,
                'created_at' => now(),
                'updated_at' => now(),
            ], $aiResult['weaknesses']);

            Weakness::insert($weaknesses);
        }

        if (!empty($aiResult['technical_skills'])) {
            $skills = array_map(fn($skill) => [
                'parsed_data_id' => $parsedData->id,
                'description' => $skill,
                'created_at' => now(),
                'updated_at' => now(),
            ], $aiResult['technical_skills']);

            Skill::insert($skills);
        }

        if (!empty($aiResult['certificates'])) {
            $certificates = array_map(fn($cert) => [
                'parsed_data_id' => $parsedData->id,
                'description' => $cert,
                'created_at' => now(),
                'updated_at' => now(),
            ], $aiResult['certificates']);

            Certificate::insert($certificates);
        }

        return $parsedData;
    }
}
