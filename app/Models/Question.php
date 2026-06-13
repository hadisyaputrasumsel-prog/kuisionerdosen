<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['section', 'question_text', 'is_active', 'order_num'];
}
