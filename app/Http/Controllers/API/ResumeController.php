<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resume;
use App\Utils\ResumeTransactionUtil;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Exports\ResumeDataExport;
use Maatwebsite\Excel\Facades\Excel;

class ResumeController extends Controller
{
    public function exportResumes()
    {
        return Excel::download(new ResumeDataExport, 'resumes_data.xlsx');
    }
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

            $text = $this->resumeTransactionUtil->extractTedx($request);

            $aiResult = $this->askGemini($text);

            $cleanJson = str_replace(['```json', '```'], '', $aiResult);

            $aiResult = json_decode(trim($cleanJson), true);

            $parsedData = $this->resumeTransactionUtil->newStoreDataSummarizeData($aiResult, $resume);

            $job_recommendations = $this->resumeTransactionUtil->findBestMatch($aiResult, $this->endpoint, $parsedData->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Resume uploaded and analyzed successfully',
                'parsed_data' => $aiResult,
                'job_recommendations' => $job_recommendations
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
            $request->validate([
                'pdf' => 'required|mimes:pdf,doc,docx|max:10240',
            ]);

            $user_id = Auth::id() ?? 1;

            $path = $request->file('pdf')->store('pdf', 'public');
            $url = '/storage/' . $path;

            $resume = Resume::create([
                'user_id' => $user_id,
                'file_path' => $url,
            ]);

            $text = $this->resumeTransactionUtil->extractTedx($request);
            $ai_result_raw = $this->askGemini($text);

            $cleanJson = str_replace(['```json', '```'], '', $ai_result_raw);
            $ai_result = json_decode(trim($cleanJson), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('AI generated invalid JSON: ' . json_last_error_msg());
            }

            $parsed_data = DB::transaction(function () use ($ai_result, $resume) {
                return $this->resumeTransactionUtil->newStoreDataSummarizeData($ai_result, $resume);
            });

            //get recomment job using gemini
            // $job_recommendations = $this->resumeTransactionUtil->findBestMatch($ai_result, $this->endpoint, $parsed_data->id);
            $job_recommendations = [];

            // $job_recommendations = $this->resumeTransactionUtil->getRecommendationsJobs($user_id, $resume, $parsed_data);

            return view('test_pdf.pdf_upload', compact('ai_result', 'job_recommendations'));
        } catch (Exception $e) {
            \Log::error('Summarize PDF Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to process resume: ' . $e->getMessage());
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
            throw new Exception('Gemini Error: ' . $response->json('error.message'));
        }
        $data = $response->json();

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response generated.';
    }

    public function getProfileData($user_id)
    {
        try {

            //get profile data
            return $this->resumeTransactionUtil->getProfileData($user_id);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve profile data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRecommendations()
    {
        try {
            $user_id = 1;

            $response = $this->resumeTransactionUtil->getRecommendationsJobs($user_id);

            return response()->json([
                'status' => 'success',
                'matched_jobs' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        //
    }
}
