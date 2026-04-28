<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRecommendation extends Model
{
    protected $fillable = ['parsed_data_id', 'job_title', 'match_percentage'];

    public function parsedData()
    {
        return $this->belongsTo(ParsedData::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'title', 'job_title')
            ->orderBy('salary_max', 'desc')
            ->orderBy('salary_min', 'desc');
    }
}
