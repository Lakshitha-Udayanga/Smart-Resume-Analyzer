<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\ParsedData;
use App\Models\Resume;
use App\Models\User;
use App\Utils\ResumeTransactionUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JobRecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_saves_job_recommendations_from_api()
    {
        // 1. Setup
        $user = User::factory()->create();
        $resume = Resume::create(['user_id' => $user->id, 'file_path' => '/test.pdf']);
        $parsedData = ParsedData::create(['resume_id' => $resume->id, 'summary_text' => 'Test summary']);

        // Create a job for the foreign key
        $job = Job::create([
            'title' => 'Software Engineer',
            'company_name' => 'Softlogic Holdings',
            'job_type' => 'Full-Time',
        ]);

        // Mock the API response
        Http::fake([
            'http://50.17.87.226:5000/match-jobs' => Http::response([
                [
                    "id" => $job->id,
                    "title" => "Software Engineer",
                    "company_name" => "Softlogic Holdings",
                    "final_score" => 47.2,
                    "matched_skills" => ["react", "laravel"]
                ]
            ], 200)
        ]);

        // 2. Action
        $util = new ResumeTransactionUtil();
        $result = $util->getRecommendationsJobs($user->id);

        // 3. Assert
        $this->assertDatabaseHas('job_recommendations', [
            'parsed_data_id' => $parsedData->id,
            'job_id' => $job->id,
            'job_title' => 'Software Engineer',
            'company_name' => 'Softlogic Holdings',
            'match_score' => 47.2,
        ]);

        $this->assertCount(1, $result);
    }
}
