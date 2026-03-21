<?php

namespace App\Exports;

use App\Models\TrainingDataSet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TrainingDataExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return TrainingDataSet::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Certificates',
            'Experiences',
            'Skills',
            'Matching Job List',
            'Created At'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $this->formatJson($row->certificates),
            $this->formatJson($row->experiences),
            $this->formatJson($row->skills),
            $this->formatJson($row->matching_job_list),
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    private function formatJson($json)
    {
        $data = json_decode($json, true);
        if (is_array($data)) {
            return implode(', ', $data);
        }
        return $json;
    }
}
