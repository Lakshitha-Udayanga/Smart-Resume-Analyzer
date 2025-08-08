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
        return $this->hasMany(Strength::class);
    }

    public function weaknesses()
    {
        return $this->hasMany(Weakness::class);
    }

    public function jobRecommendations()
    {
        return $this->hasMany(JobRecommendation::class);
    }
}
