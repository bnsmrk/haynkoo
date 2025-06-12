<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    
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
}
