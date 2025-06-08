<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
    Route::get('/user', [AuthController::class, 'getUser'])->middleware('auth:sanctum');

    // Route untuk mendapatkan semua guru (viewAny)
    Route::get('/teachers', [TeacherController::class, 'index'])
        ->middleware('permission:viewAny-teacher');

    // Route untuk melihat detail guru (view)
    Route::get('/teachers/{teacher}', [TeacherController::class, 'show'])
        ->middleware('permission:view-teacher');

    // Route untuk membuat guru baru (create)
    Route::post('/teachers', [TeacherController::class, 'store'])
        ->middleware('permission:create-teacher');

    // Route untuk mengupdate guru (update)
    Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])
        ->middleware('permission:update-teacher');

    // Route untuk menghapus guru (delete)
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])
        ->middleware('permission:delete-teacher');

    // Route untuk menghapus beberapa guru secara bulk
    Route::delete('/teachers/bulk-delete', [TeacherController::class, 'bulkDelete'])
            ->middleware('permission:delete-teacher');

    // Route untuk mengembalikan guru yang dihapus secara soft (restore)
    Route::post('/teachers/{teacher}/restore', [TeacherController::class, 'restore'])
        ->middleware('permission:restore-teacher');

    // Route untuk menghapus guru secara permanen (forceDelete)
    Route::delete('/teachers/{teacher}/force-delete', [TeacherController::class, 'forceDelete'])
        ->middleware('permission:forceDelete-teacher');
});

Route::apiResource('students', \App\Http\Controllers\StudentController::class);
Route::apiResource('questions', \App\Http\Controllers\QuestionController::class);
Route::apiResource('subjects', \App\Http\Controllers\SubjectController::class);
Route::apiResource('grades', \App\Http\Controllers\GradeController::class);
Route::apiResource('academic-years', \App\Http\Controllers\AcademicYearController::class);
Route::apiResource('student-grades', \App\Http\Controllers\StudentGradeController::class);
Route::apiResource('teacher-subjects', \App\Http\Controllers\TeacherSubjectController::class);
Route::apiResource('exams', \App\Http\Controllers\ExamController::class);
Route::apiResource('literatures', \App\Http\Controllers\LiteratureController::class); // Tambahkan route untuk Literature
Route::get('/question-types', [\App\Http\Controllers\API\QuestionTypeController::class, 'index']);
Route::get('/time-options', [\App\Http\Controllers\API\TimeController::class, 'index']);
Route::get('/score-options', [\App\Http\Controllers\API\ScoreController::class, 'index']);
