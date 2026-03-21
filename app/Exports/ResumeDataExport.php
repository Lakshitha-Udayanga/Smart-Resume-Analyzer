<?php

namespace App\Exports;

use App\Models\Resume;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ResumeDataExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Resume::with([
            'parsedData.technical_skills',
            'parsedData.experiences',
            'parsedData.certificates'
        ])->get();
    }

    public function headings(): array
    {
        return [
            'Skills',
            'Experiences',
            'Certificates',
        ];
    }

    /**
     * @param Resume $resume
     */
    public function map($resume): array
    {
        $parsedData = $resume->parsedData;

        $skills = $parsedData ? $parsedData->technical_skills->pluck('description')->join(', ') : '';
        $experiences = $parsedData ? $parsedData->experiences->pluck('description')->join(', ') : '';
        $certificates = $parsedData ? $parsedData->certificates->pluck('description')->join(', ') : '';

        return [
            $skills,
            $experiences,
            $certificates,
        ];
    }
}
