<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resume;
use App\Utils\ResumeTransactionUtil;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{
    protected $resumeTransactionUtil;

    protected $moduleUtil;

    public function __construct(ResumeTransactionUtil $resumeTransactionUtil)
    {
        $this->resumeTransactionUtil = $resumeTransactionUtil;
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

            // Call AI parsing service (Python API)
            $aiResult = $this->resumeTransactionUtil->callAISummarizePdf($file);

            // Save parsed data to DB
            $data = $this->resumeTransactionUtil->storeSummarizeData($aiResult, $resume);

            // foreach ($aiResult['job_recommendations'] ?? [] as $job) {
            //     JobRecommendation::create([
            //         'parsed_data_id' => $data->id,
            //         'job_title' => $job['job_title'],
            //         'job_description' => $job['job_description'],
            //     ]);
            // }

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
            $file = $request->file('pdf');

            $path = $request->file('pdf')->store('pdf', 'public');

            $url = '/storage/' . $path;

            $resume = Resume::create([
                'user_id' => 4,
                'file_path' => $url,
            ]);

            $aiResult = $this->resumeTransactionUtil->callAISummarizePdf($file);

            $data = $this->resumeTransactionUtil->storeSummarizeData($aiResult, $resume);

            return view('test_pdf.pdf_upload', compact('aiResult'));
        } catch (\Illuminate\Http\Client\RequestException $e) {
            return response()->json([
                'error' => 'AI MODULE failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getRecommentationJobs()
    {
        $client = new Client();
        $resume = Resume::where('user_id', 4)->latest()->first();

        if (!$resume) {
            return response()->json(['error' => 'Resume not found'], 404);
        }

        $parsed = $resume->parsedData;

        $data = [
            'technical_skills' => $parsed->technicalSkills->pluck('description')->toArray() ?? [],
            'soft_skills'      => $parsed->softSkills->pluck('description')->toArray() ?? [],
            'certificates'     => $parsed->certificates->pluck('description')->toArray() ?? [],
            'strengths'        => $parsed->strengths->pluck('description')->toArray() ?? [],
            'summary'          => $parsed->summary_text ?? '',
        ];
        try {
            $response = $client->post('http://54.205.147.241:5000/recommend-jobs', [
                'json' => $data
            ]);

            $result = json_decode($response->getBody(), true);
            dd($result);

            return response()->json($result);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return response()->json([
                'error' => 'Failed to fetch job recommendations',
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
