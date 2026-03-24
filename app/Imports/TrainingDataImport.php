<?php

namespace App\Imports;

use App\Models\TrainingDataSet;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TrainingDataImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Handle skills (comma or pipe separated string to JSON array)
        $skills = isset($row['skills']) ? array_map('trim', explode(',', $row['skills'])) : [];
        if (empty($skills) && isset($row['skills'])) {
             $skills = array_map('trim', explode('|', $row['skills']));
        }

        // Handle experiences
        $experiences = isset($row['experiences']) ? array_map('trim', explode('|', $row['experiences'])) : [];
        if (empty($experiences) && isset($row['experiences'])) {
             $experiences = array_map('trim', explode(',', $row['experiences']));
        }

        // Handle certificates
        $certificates = isset($row['certificates']) ? array_map('trim', explode(',', $row['certificates'])) : [];
         if (empty($certificates) && isset($row['certificates'])) {
             $certificates = array_map('trim', explode('|', $row['certificates']));
        }

        // Handle matching_job_list
        $matching_job_list = isset($row['matching_job_list']) ? array_map('trim', explode(',', $row['matching_job_list'])) : [];
         if (empty($matching_job_list) && isset($row['matching_job_list'])) {
             $matching_job_list = array_map('trim', explode('|', $row['matching_job_list']));
        }

        return new TrainingDataSet([
            'certificates'      => json_encode($certificates),
            'experiences'       => json_encode($experiences),
            'skills'            => json_encode($skills),
            'matching_job_list' => json_encode($matching_job_list),
        ]);
    }
}
