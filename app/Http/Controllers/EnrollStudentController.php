<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\YearLevel;
use Illuminate\Http\Request;

class EnrollStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch students with the role of "student"
        $students = User::where('role', 'student')->get(['id', 'name', 'email']);

        // Fetch all grade levels
        $yearLevels = YearLevel::all(['id', 'year_level']);

        // Fetch all sections, including their associated grade level
        $sections = Section::all(['id', 'section_name', 'year_level_id']);

        // Fetch all subjects
        $subjects = Subject::all(['id', 'subject_name', 'year_level_id', 'section_id']);

        // Pass data to the frontend using Inertia
        return Inertia::render('Enroll/Index', [
            'students' => $students,
            'yearLevels' => $yearLevels,
            'sections' => $sections,
            'subjects' => $subjects, // Pass subjects to the frontend
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id', // Ensure the user exists in the users table
            'year_level' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'subject' => 'required|string|max:255', // Validate subject selection
        ]);

        // Retrieve the selected year level and section from the database
        $yearLevel = YearLevel::where('year_level', $validated['year_level'])->first();
        $section = Section::where('section_name', $validated['section'])
                          ->where('year_level_id', $yearLevel->id)
                          ->first();

        // Fetch the subject based on the selected section and year level
        $subject = Subject::where('subject_name', $validated['subject'])
                          ->where('year_level_id', $yearLevel->id)
                          ->where('section_id', $section->id)
                          ->first();

        // Check if the section exists for the selected year level
        if (!$section) {
            return redirect()->back()->with('error', 'Section not found for the selected year level.');
        }

        // Check if the subject exists for the selected section and year level
        if (!$subject) {
            return redirect()->back()->with('error', 'Subject not found for the selected section and year level.');
        }

        // Save the enrollment data in the students table
        Student::create([
            'user_id' => $validated['user_id'],
            'year_level' => $validated['year_level'],
            'subject' => $validated['subject'], // Store the selected subject
            'section_id' => $section->id,  // Storing the section ID for reference
            'subject_id' => $subject->id,  // Storing the subject ID for reference
            'section_id' => $section->id,  // <-- This column is missing in DB
            'subject_id' => $subject->id,
        ]);

        // Return a success response
        return redirect()->route('enroll.index')->with('success', 'Student enrolled successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Fetch the specific enrollment
        $enrollment = Student::with('user')->findOrFail($id);

        // Return the enrollment details
        return Inertia::render('Enroll/Show', [
            'enrollment' => $enrollment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Fetch the specific enrollment
        $enrollment = Student::findOrFail($id);

        // Return the edit form
        return Inertia::render('Enroll/Edit', [
            'enrollment' => $enrollment,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'year_level' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'subject' => 'required|string|max:255', // Validate subject selection
        ]);

        // Update the enrollment data
        $enrollment = Student::findOrFail($id);
        $enrollment->update($validated);

        // Return a success response
        return redirect()->back()->with('success', 'Enrollment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete the specific enrollment
        $enrollment = Student::findOrFail($id);
        $enrollment->delete();

        // Return a success response
        return redirect()->back()->with('success', 'Enrollment deleted successfully!');
    }
}
