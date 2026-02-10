<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'company_name',
        'category',
        'job_type',
        'location',
        'salary_min',
        'salary_max',
        'experience_level',
        'skills',
        'description',
        'status',
        'post_date',
        'closing_date'
    ];
}
