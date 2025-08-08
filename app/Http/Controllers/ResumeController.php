<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resume;
use App\Models\ParsedData;
use App\Models\Strength;
use App\Models\Weakness;
use App\Models\JobRecommendation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ResumeController extends Controller
{

    public function index()
    {
        //
    }

    public function upload(Request $request)
    {
        $request->validate([
            'resume' => 'required|mimes:pdf,doc,docx|max:5120',
        ]);

        $file = $request->file('resume');
        $path = $file->store('resumes');

        $resume = Resume::create([
            'user_id' => $request->user()->id,
            'file_path' => $path,
        ]);

        // Extract text from resume (you can add package or Python microservice)
        $text = $this->extractText(storage_path('app/' . $path));

        // Call AI parsing service (Python API)
        $aiResult = $this->callAIService($text);

        // Save parsed data to DB
        $parsedData = ParsedData::create([
            'resume_id' => $resume->id,
            'summary_text' => $aiResult['summary'] ?? '',
        ]);

        foreach ($aiResult['strengths'] ?? [] as $strength) {
            Strength::create([
                'parsed_data_id' => $parsedData->id,
                'description' => $strength,
            ]);
        }

        foreach ($aiResult['weaknesses'] ?? [] as $weakness) {
            Weakness::create([
                'parsed_data_id' => $parsedData->id,
                'description' => $weakness,
            ]);
        }

        foreach ($aiResult['job_recommendations'] ?? [] as $job) {
            JobRecommendation::create([
                'parsed_data_id' => $parsedData->id,
                'job_title' => $job['job_title'],
                'job_description' => $job['job_description'],
            ]);
        }

        return response()->json([
            'message' => 'Resume uploaded and analyzed successfully',
            'parsed_data' => $parsedData->load('strengths', 'weaknesses', 'jobRecommendations'),
        ]);
    }

    private function extractText($filePath)
    {
        // Basic example: use shell command 'pdftotext' for PDF files
        if (str_ends_with($filePath, '.pdf')) {
            $output = null;
            $retval = null;
            exec("pdftotext " . escapeshellarg($filePath) . " -", $output, $retval);
            return implode("\n", $output);
        }

        // For doc/docx you can integrate PHP libraries or services

        return '';
    }

    private function callAIService($text)
    {
        // Call your Python Flask API or any AI microservice
        $response = Http::post('http://localhost:5000/analyze', [
            'text' => $text
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return [
            'summary' => '',
            'strengths' => [],
            'weaknesses' => [],
            'job_recommendations' => [],
        ];
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
