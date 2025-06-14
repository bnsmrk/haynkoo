<?php

namespace App\Models;

use App\Models\Section;
use App\Models\Subject;
use App\Models\Question;
use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
       use HasFactory;
    protected $fillable = [
    'title',
    'date_time',
    'year_level_id',
    'section_id',
    'subject_id',
];
public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class, 'year_level_id');
    }
 public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

}
