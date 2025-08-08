<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
   protected $fillable = ['user_id', 'file_path'];

    public function parsedData()
    {
        return $this->hasOne(ParsedData::class);
    }
}
