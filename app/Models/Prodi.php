<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    protected $fillable = ['name'];
    public function jadwals() { return $this->hasMany(Jadwal::class); }
}
