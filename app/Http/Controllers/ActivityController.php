<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Activity;
use App\Models\YearLevel;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    // Display the activity form
    public function index()
    {
        // Fetch the necessary data to show in the form
        $yearLevels = YearLevel::all();
        $sections = Section::all();
        $subjects = Subject::all();

        return Inertia::render('Activity/Index', [
            'yearLevels' => $yearLevels,
            'sections' => $sections,
            'subjects' => $subjects,
        ]);
    }

    // Store the new activity
   public function store(Request $request)
{
    // Validate the incoming request data
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'date' => 'required|date',
        'time' => 'required|date_format:H:i',
        'year_level' => 'required|exists:year_levels,year_level',
        'section' => 'required|exists:sections,section_name',
        'subject' => 'required|exists:subjects,subject_name',
    ]);

    // Combine the date and time into a single date_time value
    $dateTime = $validated['date'] . ' ' . $validated['time'];

    // Get the year_level_id, section_id, and subject_id
    $yearLevel = YearLevel::where('year_level', $validated['year_level'])->first();
    $section = Section::where('section_name', $validated['section'])->first();
    $subject = Subject::where('subject_name', $validated['subject'])->first();

    // Check if the related records exist
    if (!$yearLevel || !$section || !$subject) {
        return back()->withErrors('Unable to find related records.');
    }

    // Create and store the new activity in the database
    Activity::create([
        'title' => $validated['title'],
        'date_time' => $dateTime, // Store as a single column
        'year_level_id' => $yearLevel->id, // Set the correct year_level_id
        'section_id' => $section->id, // Set the correct section_id
        'subject_id' => $subject->id, // Set the correct subject_id
    ]);

    // Return a success response
    return redirect()->route('activity.index')->with('success', 'Activity created successfully.');
}


}
