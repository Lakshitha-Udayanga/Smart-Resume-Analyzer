<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingDataSet extends Model
{
    use HasFactory;

    protected $table = 'traning_data_sets';

    protected $fillable = [
        'certificates',
        'experiences',
        'skills',
        'matching_job_list',
    ];
}
