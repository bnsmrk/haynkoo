<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\YearLevel;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['yearLevel', 'section', 'subject'])->get();
        $yearLevels = YearLevel::all();
        $sections = Section::all();
        $subjects = Subject::all();

        return Inertia::render('Activity/Index', [
            'activities' => $activities,
            'yearLevels' => $yearLevels,
            'sections' => $sections,
            'subjects' => $subjects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        Activity::create([
            'title' => $validated['title'],
            'date_time' => $validated['date'] . ' ' . $validated['time'],
            'year_level_id' => $validated['year_level_id'],
            'section_id' => $validated['section_id'],
            'subject_id' => $validated['subject_id'],
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $activity->update([
            'title' => $validated['title'],
            'date_time' => $validated['date'] . ' ' . $validated['time'],
            'year_level_id' => $validated['year_level_id'],
            'section_id' => $validated['section_id'],
            'subject_id' => $validated['subject_id'],
        ]);

        return redirect()->back();
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->back();
    }
}
