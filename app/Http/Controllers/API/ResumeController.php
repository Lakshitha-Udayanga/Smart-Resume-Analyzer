<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resume;
use App\Models\User;
use App\Utils\ResumeTransactionUtil;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ResumeController extends Controller
{
    protected $resumeTransactionUtil;

    protected $moduleUtil;

    protected $geminiKey;

    protected $endpoint;

    public function __construct(ResumeTransactionUtil $resumeTransactionUtil)
    {
        $this->resumeTransactionUtil = $resumeTransactionUtil;

        $this->geminiKey = env('GEMINI_API_KEY');

        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->geminiKey}";
    }

    public function upload(Request $request, $user_id)
    {
        try {

            $request->validate([
                'pdf' => 'required|mimes:pdf,doc,docx|max:5120',
            ]);

            $path = $request->file('pdf')->store('pdf', 'public');

            $url = '/storage/' . $path;

            $resume = Resume::create([
                'user_id' => $user_id,
                'file_path' => $url,
            ]);

            $file = $request->file('pdf');

            // $aiResult = $this->resumeTransactionUtil->callAISummarizePdf($file);

            // $data = $this->resumeTransactionUtil->storeSummarizeData($aiResult, $resume);

            $text = $this->resumeTransactionUtil->extractTedx($request);

            $aiResult = $this->askGemini($text);

            $cleanJson = str_replace(['```json', '```'], '', $aiResult);

            $aiResult = json_decode(trim($cleanJson), true);

            $data = $this->resumeTransactionUtil->newStoreDataSummarizeData($aiResult, $resume);

            return response()->json([
                'status' => 'success',
                'message' => 'Resume uploaded and analyzed successfully',
                'parsed_data' => $aiResult
            ], 201);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json([
                'error' => 'CV analyzed failed',
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
        try {
            $path = $request->file('pdf')->store('pdf', 'public');

            $url = '/storage/' . $path;

            $resume = Resume::create([
                'user_id' => 4,
                'file_path' => $url,
            ]);

            $text = $this->resumeTransactionUtil->extractTedx($request);

            $aiResult = $this->askGemini($text);

            $cleanJson = str_replace(['```json', '```'], '', $aiResult);

            $aiResult = json_decode(trim($cleanJson), true);

            $data = $this->resumeTransactionUtil->newStoreDataSummarizeData($aiResult, $resume);

            return view('test_pdf.pdf_upload', compact('aiResult'));
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json([
                'error' => 'AI MODULE failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function askGemini($text)
    {
        $base64Text = base64_encode($text);

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

        $response = Http::timeout(120)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

        if ($response->failed()) {
            return "Error: " . $response->body();
        }

        $data = $response->json();

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated.';
    }

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

    /**
     * Remove the specified resource from storage.
     */
    public function getProfileData($user_id)
    {
        try {
            $user = User::with(['cv_lists' => function ($query) {
                $query->latest();
            }, 'cv_lists.parsedData' => function ($query) {
                $query->with(['strengths', 'weaknesses', 'technical_skills', 'soft_skills', 'certificates', 'experiences']);
            }])->findOrFail($user_id);

            $latestResume = $user->cv_lists->first();

            if (!$latestResume || !$latestResume->parsedData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No parsed resume found for this user'
                ], 404);
            }

            $parsedData = $latestResume->parsedData;

            return response()->json([
                'status' => 'success',
                'data' => [
                    'strengths' => $parsedData->strengths->pluck('description'),
                    'weaknesses' => $parsedData->weaknesses->pluck('description'),
                    'skills' => $parsedData->technical_skills->pluck('description'),
                    'soft_skills' => $parsedData->soft_skills->pluck('description'),
                    'certificates' => $parsedData->certificates->pluck('description'),
                    'experiences' => $parsedData->experiences->pluck('description'),
                    'summary' => $parsedData->summary_text
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve profile data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        //
    }
}
