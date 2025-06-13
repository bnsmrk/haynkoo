<?php

namespace App\Models;

use App\Models\Student;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
   use HasFactory;

    // Table associated with the model
    protected $table = 'sections';

    // Define the relationship with the YearLevel model
    public function yearLevel()
    {
        return $this->belongsTo(YearLevel::class);  // A Section belongs to one YearLevel
    }

    // Define the relationship with the Subject model
    public function subjects()
    {
        return $this->hasMany(Subject::class);  // A Section has many Subjects
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
