<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
 use HasFactory;

    protected $fillable = ['type', 'question', 'options', 'answer_key'];

    protected $casts = [
        'options' => 'array', // Automatically cast JSON to array
    ];
}
