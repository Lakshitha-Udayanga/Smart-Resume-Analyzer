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

    public function upload(Request $request)
    {
        return response()->json($request);

        $request->validate([
            'resume' => 'required|mimes:pdf,doc,docx|max:5120',
        ]);

        $file = $request->file('resume');
        $path = $file->store('resumes');


        $parser = new Parser();
        $pdfText = $parser->parseFile($request->file('pdf')->getRealPath())->getText();

        return response()->json($pdfText);

        $resume = Resume::create([
            'user_id' => $request->user()->id,
            'file_path' => $path,
        ]);

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
