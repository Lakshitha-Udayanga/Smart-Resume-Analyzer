<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strength extends Model
{
    protected $fillable = ['parsed_data_id', 'description'];

    public function parsedData()
    {
        return $this->belongsTo(ParsedData::class);
    }
}
