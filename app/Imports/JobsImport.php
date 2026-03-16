<?php

namespace App\Imports;

use App\Models\Job;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JobsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Job([
            'title'            => $row['job_position_name'],
            'company_name'     => $row['company_name'],
            'category'         => $row['department_category'],
            'job_type'         => $row['job_type'] ?? 'Full-Time',
            'location'         => $row['job_location'],
            'salary_min'       => $row['salary_min'],
            'salary_max'       => $row['salary_max'],
            'experience_level' => $row['experience_level'],
            'skills'           => $row['skills'],
            'description'      => $row['description'] ?? null,
            'status'           => $row['status'] ?? 'active',
            'post_date'        => isset($row['post_date']) ? Carbon::parse($row['post_date']) : null,
            'closing_date'     => isset($row['closing_date']) ? Carbon::parse($row['closing_date']) : null,
        ]);
    }
}
