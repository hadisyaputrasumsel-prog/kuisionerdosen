<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'jadwal_id',
        'answers',
        'saran'
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }
}
