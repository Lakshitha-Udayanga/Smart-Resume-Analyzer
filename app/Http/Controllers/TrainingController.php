<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Resume;
use App\Models\TrainingDataSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TrainingDataExport;

class TrainingController extends Controller
{
    protected $geminiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->geminiKey = env('GEMINI_API_KEY');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$this->geminiKey}";
    }

    public function index()
    {
        $perPage = request()->input('per_page', 10);
        $search = request()->input('search');

        $query = TrainingDataSet::latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('certificates', 'LIKE', "%{$search}%")
                    ->orWhere('experiences', 'LIKE', "%{$search}%")
                    ->orWhere('skills', 'LIKE', "%{$search}%")
                    ->orWhere('matching_job_list', 'LIKE', "%{$search}%");
            });
        }

        $trainingData = $query->paginate($perPage)->appends(request()->all());

        return view('training.index', compact('trainingData', 'perPage', 'search'));
    }

    public function process()
    {
        try {
            $jobs = Job::select('title', 'education_certificate', 'skills')->get();

            if ($jobs->isEmpty()) {
                return back()->with('error', 'No jobs found in the database. Please import jobs first.');
            }

            $jobDataStrings = $jobs->map(function ($job) {
                return "Title: {$job->title}, Required Education: {$job->education_certificate}, Required Skills: {$job->skills}";
            })->toArray();

            $resumes = Resume::with(['parsedData.certificates', 'parsedData.experiences', 'parsedData.technical_skills'])->whereNull('is_traning_data')->get();

            if ($resumes->isEmpty()) {
                return back()->with('error', 'No resumes found in the database.');
            }

            $count = 0;
            foreach ($resumes as $resume) {
                if (!$resume->parsedData) continue;

                $certs = $resume->parsedData->certificates->pluck('description')->toArray();
                $exps = $resume->parsedData->experiences->pluck('description')->toArray();
                $skills = $resume->parsedData->technical_skills->pluck('description')->toArray();

                if (empty($certs) && empty($exps) && empty($skills)) continue;

                $prompt = "
                    Candidate Qualifications:
                    Certificates: " . implode(', ', $certs) . "
                    Experiences: " . implode(', ', $exps) . "
                    Skills: " . implode(', ', $skills) . "

                    Available Jobs (with requirements):
                    " . implode("\n", $jobDataStrings) . "

                    Based on the candidate's qualifications, identify EXACTLY 15 most suitable job titles from the provided list.
                    If there are fewer than 15 jobs in the list, return all of them.
                    If there are many jobs, pick the 15 best matches.
                    The output MUST be a VALID JSON array of strings containing ONLY the job titles.
                ";

                $response = Http::timeout(180)->post($this->endpoint, [
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
                    $matchedJobs = json_decode(trim($responseText), true);
                    if (is_array($matchedJobs)) {
                        TrainingDataSet::create([
                            'certificates' => json_encode($certs),
                            'experiences' => json_encode($exps),
                            'skills' => json_encode($skills),
                            'matching_job_list' => json_encode($matchedJobs),
                        ]);
                        $count++;

                        Resume::where('id', $resume->id)->update([
                            'is_traning_data' => 1
                        ]);
                    }
                } else {
                    Log::error("Gemini API Error for Resume ID {$resume->id}: " . $response->body());
                }
            }

            if ($count > 0) {
                return back()->with('success', "Training data generation completed. Created {$count} records in traning_data_sets table.");
            } else {
                return back()->with('info', "Process completed but no new training records were created. Check if resumes have parsed data.");
            }
        } catch (\Exception $e) {
            Log::error("Data Training Error: " . $e->getMessage());
            return back()->with('error', 'An error occurred during training data generation: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new TrainingDataExport, 'training_dataset_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
