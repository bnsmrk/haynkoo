<?php

namespace App\Models;

use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
 use HasFactory;

    protected $fillable = ['subject_name', 'year_level_id', 'section_id'];

    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
