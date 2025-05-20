<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'getUser'])->middleware('auth:sanctum');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware('auth:sanctum')->group(function () {
});
Route::apiResource('teachers', \App\Http\Controllers\TeacherController::class);
Route::apiResource('students', \App\Http\Controllers\StudentController::class);
Route::apiResource('subjects', \App\Http\Controllers\SubjectController::class);
Route::apiResource('grades', \App\Http\Controllers\GradeController::class);
Route::apiResource('academic-years', \App\Http\Controllers\AcademicYearController::class);
Route::apiResource('student-grades', \App\Http\Controllers\StudentGradeController::class);
Route::apiResource('teacher-subjects', \App\Http\Controllers\TeacherSubjectController::class);
