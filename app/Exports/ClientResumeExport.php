<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClientResumeExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return User::where('is_system_user', 0)
            ->with([
                'cv_lists' => function ($query) {
                    $query->latest();
                },
                'cv_lists.parsedData.technical_skills',
                'cv_lists.parsedData.experiences',
                'cv_lists.parsedData.certificates'
            ])
            ->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Skills',
            'Experiences',
            'Certificates',
        ];
    }

    /**
     * @param User $user
     */
    public function map($user): array
    {
        $latestResume = $user->cv_lists->first();
        $parsedData = $latestResume ? $latestResume->parsedData : null;

        $skills = $parsedData ? $parsedData->technical_skills->pluck('description')->join(', ') : '';
        $experiences = $parsedData ? $parsedData->experiences->pluck('description')->join(', ') : '';
        $certificates = $parsedData ? $parsedData->certificates->pluck('description')->join(', ') : '';

        return [
            $user->name,
            $user->email,
            $skills,
            $experiences,
            $certificates,
        ];
    }
}
