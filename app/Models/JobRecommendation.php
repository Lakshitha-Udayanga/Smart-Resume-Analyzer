<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRecommendation extends Model
{
    protected $fillable = ['parsed_data_id', 'job_title', 'job_description'];

    public function parsedData()
    {
        return $this->belongsTo(ParsedData::class);
    }
}
