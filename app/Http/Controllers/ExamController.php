<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Question;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Exam/Index');
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
        $validated = $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.type' => 'required|string|in:multiple-choice,true-false,checkboxes',
            'questions.*.question' => 'required|string|max:255',
            'questions.*.options' => 'nullable|array',
            'questions.*.options.*' => 'nullable|string|max:255',
            'questions.*.answerKey' => 'required|string',
        ]);

        foreach ($validated['questions'] as $questionData) {
            Question::create([
                'type' => $questionData['type'],
                'question' => $questionData['question'],
                'options' => $questionData['type'] !== 'true-false' ? json_encode($questionData['options']) : null,
                'answer_key' => $questionData['answerKey'],
            ]);
        }

        return response()->json(['message' => 'Questions added successfully!'], 201);
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
