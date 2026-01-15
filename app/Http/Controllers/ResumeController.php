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
use Smalot\PdfParser\Parser;

class ResumeController extends Controller
{

    public function index()
    {
        //
    }

    public function upload(Request $request, $user_id)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf,doc,docx|max:5120',
        ]);

        $path = $request->file('pdf')->store('pdf', 'public');

        $url = asset('storage/' . $path);

        $resume = Resume::create([
            'user_id' => $user_id,
            'file_path' => $url,
        ]);

        $parser = new Parser();

        $pdfText = $parser->parseFile($request->file('pdf')->getRealPath())->getText();

        $resume = Resume::create([
            'user_id' => $request->user()->id,
            'file_path' => $path,
        ]);

        return response()->json($resume);

        // Call AI parsing service (Python API)
        $aiResult = $this->callAIService($pdfText);

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

    private function callAIService($text)
    {
        $response = Http::timeout(300)->post('http://127.0.0.1:11434/api/generate', [
            'model' => 'gemma3:4b',
            'prompt' => "Analyze the following text and provide a JSON object with the following keys:
                - skills: list of skills
                - strengths: list of strengths
                - weaknesses: list of weaknesses
                - summary: a short summary of the text
                - certificates: list of certificates if any
                Text:
                \"\"\"$text\"\"\"",
            'stream' => false,
        ]);

        if ($response->successful()) {
            $result = $response->json();

            $relevantLabels = [
                'skills' => $result['skills'] ?? [],
                'strengths' => $result['strengths'] ?? [],
                'weaknesses' => $result['weaknesses'] ?? [],
                'summary' => $result['summary'] ?? '',
                'certificates' => $result['certificates'] ?? [],
            ];

            return $relevantLabels;
        }

        return [
            'skills' => [],
            'strengths' => [],
            'weaknesses' => [],
            'summary' => '',
            'certificates' => [],
        ];
    }

    public function askOllama(Request $request)
    {
        $prompt = $request->input('prompt', 'who are you');

        try {
            $response = Http::timeout(120)->post('http://127.0.0.1:11434/api/generate', [
                'model' => 'gemma3:4b', // your local model
                'prompt' => $prompt,
                'stream' => false
            ]);

            return response()->json($response->json());
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json([
                'error' => 'Ollama request failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function viewIndexPdf()
    {
        return view('test_pdf.pdf_upload');
    }
    public function summarizePdf(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:10240',
        ]);

        // $pdf = $request->file('pdf');
        // // Extract text from PDF
        // $parser = new Parser();
        // $pdfText = $parser->parseFile($pdf->getRealPath())->getText();
        // $pdfText = substr($pdfText, 0, 4000); // adjust as needed
        // Send to Ollama for summary
        try {
            // $response = Http::timeout(120)->post('http://127.0.0.1:11434/api/generate', [
            //     'model' => 'gemma3:4b',
            //     'prompt' => "Give me Skile the following document:\n\n$pdfText",
            //     'stream' => false,
            // ]);
            // dd($response->json());

            $parser = new Parser();
            $pdfText = $parser->parseFile($request->file('pdf')->getRealPath())->getText();
            // Split into smaller chunks
            $chunks = str_split($pdfText, 100);
            $summary = '';
            foreach ($chunks as $chunk) {
                $response = Http::timeout(300)->post('http://127.0.0.1:11434/api/generate', [
                    'model' => 'gemma3:4b',
                    'prompt' => "give me lebalize for Skile the follwing this text:\n\n$chunk",
                    'stream' => false,
                ]);
                $summary .= $response->json()['text'] ?? '';
                dd($response->json()['response']);
            }
            return response()->json(['summary' => $summary]);

            return response()->json($response->json());
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json([
                'error' => 'Ollama request failed',
                'message' => $e->getMessage()
            ], 500);
        }
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
