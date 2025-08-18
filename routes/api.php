<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileLinkingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('link-tokens/generate/{type}/{id}', [ProfileLinkingController::class, 'generateLinkToken']);
    Route::post('link-profile', [ProfileLinkingController::class, 'linkProfileAccount']);
});


Route::prefix('teachers')->middleware('auth:sanctum', 'academic.year')->group(function () {
    Route::get('/template', [TeacherController::class, 'downloadTemplate']);
    Route::post('/import', [TeacherController::class, 'import']);
    Route::get('/export', [TeacherController::class, 'export']);
    Route::delete('/bulk-delete', [TeacherController::class, 'bulkDelete']);
    Route::get('/{teacher}/link-token-status', [TeacherController::class, 'getLinkTokenStatus']);
});
Route::apiResource('teachers', \App\Http\Controllers\TeacherController::class)->middleware('academic.year');

Route::prefix('students')->middleware('auth:sanctum', 'academic.year')->group(function () {
    Route::get('/template', [StudentController::class, 'downloadTemplate']);
    Route::post('/import', [StudentController::class, 'import']);
    Route::get('/export', [StudentController::class, 'export']);
    Route::delete('/bulk-delete', [StudentController::class, 'bulkDelete']);
    Route::get('/without-grades', [StudentController::class, 'studentsWithoutGrades']);
    Route::get('/with-grades', [StudentController::class, 'studentsWithGrades']);
});
Route::apiResource('students', \App\Http\Controllers\StudentController::class)->middleware('academic.year');

Route::apiResource('questions', \App\Http\Controllers\QuestionController::class);
Route::apiResource('subjects', \App\Http\Controllers\SubjectController::class);
Route::apiResource('grades', \App\Http\Controllers\GradeController::class)->middleware('academic.year');
Route::apiResource('student-grades', \App\Http\Controllers\StudentGradeController::class);
Route::apiResource('teacher-subjects', \App\Http\Controllers\TeacherSubjectController::class);
Route::get('academic-years/active', [\App\Http\Controllers\AcademicYearController::class, 'getActive']);
Route::get('academic-years/set-active', [\App\Http\Controllers\AcademicYearController::class, 'setActive']);
Route::apiResource('academic-years', \App\Http\Controllers\AcademicYearController::class);
Route::apiResource('exams', \App\Http\Controllers\ExamController::class);
Route::apiResource('literatures', \App\Http\Controllers\LiteratureController::class); // Tambahkan route untuk Literature
Route::get('/question-types', [\App\Http\Controllers\API\QuestionTypeController::class, 'index']);
Route::get('/time-options', [\App\Http\Controllers\API\TimeController::class, 'index']);
Route::get('/score-options', [\App\Http\Controllers\API\ScoreController::class, 'index']);
