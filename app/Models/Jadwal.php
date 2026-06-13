<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jadwal extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function prodi() { return $this->belongsTo(Prodi::class); }
    public function dosen() { return $this->belongsTo(Dosen::class); }
    public function mataKuliah() { return $this->belongsTo(MataKuliah::class); }
    public function evaluations() { return $this->hasMany(Evaluation::class); }
}
