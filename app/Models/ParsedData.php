<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParsedData extends Model
{
    protected $fillable = ['resume_id', 'summary_text'];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function strengths()
    {
        return $this->hasMany(Strength::class, 'parsed_data_id');
    }

    public function weaknesses()
    {
        return $this->hasMany(Weakness::class, 'parsed_data_id');
    }

    public function technicalSkills()
    {
        return $this->hasMany(Skill::class, 'parsed_data_id');
    }

    public function softSkills()
    {
        return $this->hasMany(Skill::class, 'parsed_data_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'parsed_data_id');
    }


    public function jobRecommendations()
    {
        return $this->hasMany(JobRecommendation::class);
    }
}
