<?php

namespace Database\Seeders;

use App\Models\Job;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobSalaryUpdaterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = Job::all();

        foreach ($jobs as $job) {
            $salaryRange = $this->calculateSalary($job->title, $job->experience_level, $job->company_name);
            
            $job->update([
                'salary_min' => $salaryRange['min'],
                'salary_max' => $salaryRange['max'],
            ]);
        }

        $this->command->info('Updated ' . $jobs->count() . ' jobs with estimated salaries.');
    }

    /**
     * Calculate salary range based on title, experience level and company name for Sri Lanka.
     */
    private function calculateSalary($title, $experience, $company): array
    {
        $title = Str::lower($title);
        $experience = Str::lower($experience);
        $company = Str::lower($company);

        // Base ranges (Monthly LKR)
        $ranges = [
            'intern' => ['min' => 30000, 'max' => 60000],
            'junior' => ['min' => 80000, 'max' => 160000],
            'mid'    => ['min' => 180000, 'max' => 380000],
            'senior' => ['min' => 450000, 'max' => 850000],
            'lead'   => ['min' => 900000, 'max' => 1600000],
        ];

        // Determine Level
        $level = 'mid'; // Default

        if (Str::contains($experience, ['intern', 'trainee', 'student'])) {
            $level = 'intern';
        } elseif (Str::contains($experience, ['junior', 'associate', 'entry', '1 year', '2 years'])) {
            $level = 'junior';
        } elseif (Str::contains($experience, ['senior', 'sr', '5 years', '6 years', '7 years', '8 years'])) {
            $level = 'senior';
        } elseif (Str::contains($experience, ['lead', 'principal', 'architect', 'manager', 'head', 'vp', 'cto', '10 years'])) {
            $level = 'lead';
        } elseif (Str::contains($experience, ['mid', 'intermediate', '3 years', '4 years'])) {
            $level = 'mid';
        } else {
            // Check title for level hints if experience is vague
            if (Str::contains($title, 'intern')) $level = 'intern';
            elseif (Str::contains($title, 'junior') || Str::contains($title, 'associate')) $level = 'junior';
            elseif (Str::contains($title, 'senior') || Str::contains($title, 'sr.')) $level = 'senior';
            elseif (Str::contains($title, ['lead', 'manager', 'head', 'director'])) $level = 'lead';
        }

        $base = $ranges[$level];
        $min = $base['min'];
        $max = $base['max'];

        // Adjust based on Job Category (Role)
        $roleMultiplier = 1.0;
        
        if (Str::contains($title, ['software', 'developer', 'engineer', 'fullstack', 'backend', 'frontend', 'mobile'])) {
            $roleMultiplier = 1.2; // Tech pays more
        } elseif (Str::contains($title, ['data scientist', 'ai', 'machine learning', 'data engineer'])) {
            $roleMultiplier = 1.3; // High demand
        } elseif (Str::contains($title, ['qa', 'test', 'quality assurance'])) {
            $roleMultiplier = 1.0;
        } elseif (Str::contains($title, ['ui', 'ux', 'designer', 'graphic'])) {
            $roleMultiplier = 1.0;
        } elseif (Str::contains($title, ['hr', 'human resource', 'admin', 'coordinator'])) {
            $roleMultiplier = 0.8; // Non-tech usually pays less in tech-heavy markets
        } elseif (Str::contains($title, ['sales', 'marketing', 'seo'])) {
            $roleMultiplier = 0.9;
        } elseif (Str::contains($title, ['project manager', 'scrum master', 'product owner'])) {
            $roleMultiplier = 1.25;
        }

        $min *= $roleMultiplier;
        $max *= $roleMultiplier;

        // Adjust based on Company tier (Top SL Tech Companies)
        $topTierCompanies = ['wso2', 'virtusa', 'sysco', '99x', 'ifs', 'surge', 'gapstars', 'creative software', 'pearson', 'lseg', 'directfn', 'dialog', 'mobitel'];
        if (Str::contains($company, $topTierCompanies)) {
            $min *= 1.15;
            $max *= 1.20;
        }

        // Add some randomness so they aren't all exactly the same
        $variation = rand(-5000, 5000);
        $min += $variation;
        $max += $variation;

        // Ensure reasonable rounding (to nearest 1000)
        $min = round($min / 1000) * 1000;
        $max = round($max / 1000) * 1000;

        return ['min' => $min, 'max' => $max];
    }
}
