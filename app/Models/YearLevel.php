<?php

namespace App\Models;

use App\Models\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class YearLevel extends Model
{
 use HasFactory;

    // Table associated with the model
    protected $table = 'year_levels';

    // Define the relationship with the Section model
    public function sections()
    {
        return $this->hasMany(Section::class);  // One YearLevel has many Sections
    }
}
