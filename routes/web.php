<?php

use Inertia\Inertia;


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\TeachersController;
use App\Http\Controllers\EnrollStudentController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/questions', [QuestionController::class, 'store']);
Route::resource('exams', ExamController::class);
Route::resource('students', StudentsController::class);
Route::resource('teachers', TeachersController::class);
Route::resource('enroll', EnrollStudentController::class);
Route::resource('materials', MaterialController::class);
// Route to display the activity form (GET request)
Route::get('/activity', [ActivityController::class, 'index'])->name('activity.index');

// Route to store the activity (POST request)
Route::post('/activity', [ActivityController::class, 'store'])->name('activity.store');




// Route::get('/enroll/students', [EnrollStudentController::class, 'getStudents']);
// Route::get('/enroll/year-levels', [EnrollStudentController::class, 'getYearLevels']);
// Route::get('/enroll/sections', [EnrollStudentController::class, 'getSections']);
require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
