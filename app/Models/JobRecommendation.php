<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRecommendation extends Model
{
    protected $fillable = ['parsed_data_id', 'job_id', 'job_title', 'company_name', 'job_description', 'match_score', 'matched_skills', 'link'];

    public function parsedData()
    {
        return $this->belongsTo(ParsedData::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
