<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'jadwal_id',
        'q1',
        'q2',
        'q3',
        'q4',
        'saran'
    ];
}
