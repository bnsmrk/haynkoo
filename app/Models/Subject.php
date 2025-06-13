<?php

namespace App\Models;

use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
  use HasFactory;

    // Table associated with the model
    protected $table = 'subjects';

    // Define the relationship with the Section model
    public function section()
    {
        return $this->belongsTo(Section::class);  // A Subject belongs to one Section
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
