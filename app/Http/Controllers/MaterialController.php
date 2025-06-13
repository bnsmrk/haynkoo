<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Material;
use App\Models\YearLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $yearLevels = YearLevel::select('id', 'year_level')->get();

    $sections = Section::select('id', 'section_name', 'year_level_id')->get();

    $subjects = Subject::with('section.yearLevel')->get()->map(function ($subject) {
        return [
            'id' => $subject->id,
            'subject_name' => $subject->subject_name,
            'section_id' => $subject->section_id,
            'section_name' => $subject->section->section_name ?? null,
            'year_level_id' => $subject->section->year_level_id ?? null,
            'year_level' => $subject->section->yearLevel->year_level ?? null,
        ];
    });

    return Inertia::render('Material/Index', [
        'yearLevels' => $yearLevels,
        'sections' => $sections,
        'subjects' => $subjects,
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'year_level_id' => 'required|exists:year_levels,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'material_type' => 'required|in:learning_material,lesson_plan',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx',
        ]);

        $path = $request->file('file')->store('materials', 'public');

        Material::create([
            'teacher_id' => auth()->id(),
            'year_level_id' => $request->year_level_id,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'material_type' => $request->material_type,
            'file_path' => $path,
        ]);

        return back()->with('success', 'Material uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
