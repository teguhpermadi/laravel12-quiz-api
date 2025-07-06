<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileLinkingController;
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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', [AuthController::class, 'me']);

    // Route Teachers prefix
    Route::prefix('teachers')->group(function () {
        // Route untuk mengunduh template impor guru
        Route::get('/template', [TeacherController::class, 'downloadTemplate']);
        // Route untuk mengimport data guru
        Route::post('/import', [TeacherController::class, 'import']);
        // route untuk mengexport data guru
        Route::get('/export', [TeacherController::class, 'export']);
        // Route untuk menghapus beberapa guru secara bulk
        Route::delete('/bulk-delete', [TeacherController::class, 'bulkDelete'])->middleware('permission:delete-teacher');
        // Route untuk mendapatkan daftar guru dengan filter, sorting, dan pagination
        Route::get('/', [TeacherController::class, 'index'])->middleware('permission:viewAny-teacher');
        // Route untuk melihat detail guru (view)
        Route::get('/{teacher}', [TeacherController::class, 'show'])->middleware('permission:view-teacher');
        // Route untuk membuat guru baru (create)
        Route::post('/', [TeacherController::class, 'store'])->middleware('permission:create-teacher');
        // Route untuk mengupdate guru (update)
        Route::put('/{teacher}', [TeacherController::class, 'update'])->middleware('permission:update-teacher');
        // Route untuk menghapus guru (delete)
        Route::delete('/{teacher}', [TeacherController::class, 'destroy'])->middleware('permission:delete-teacher');
        // Route untuk mengembalikan guru yang dihapus secara soft (restore)
        Route::post('/{teacher}/restore', [TeacherController::class, 'restore'])->middleware('permission:restore-teacher');
        // Route untuk menghapus guru secara permanen (forceDelete)
        Route::delete('/{teacher}/force-delete', [TeacherController::class, 'forceDelete'])->middleware('permission:forceDelete-teacher');
    });

    // Route Students prefix
    Route::prefix('students')->group(function () {
        // Route untuk mengunduh template impor siswa
        Route::get('/template', [StudentController::class, 'downloadTemplate']);
        // Route untuk mengimport data siswa
        Route::post('/import', [StudentController::class, 'import']);
        // Route untuk mengexport data siswa
        Route::get('/export', [StudentController::class, 'export']);
        // Route untuk menghapus beberapa siswa secara bulk
        Route::delete('/bulk-delete', [StudentController::class, 'bulkDelete'])->middleware('permission:delete-student');
        // Route untuk mendapatkan daftar siswa dengan filter, sorting, dan pagination
        Route::get('/', [StudentController::class, 'index'])->middleware('permission:viewAny-student');
        // Route untuk melihat detail siswa (view)
        Route::get('/{student}', [StudentController::class, 'show']);
        // Route untuk membuat siswa baru (create)
        Route::post('/', [StudentController::class, 'store'])->middleware('permission:create-student');
        // Route untuk mengupdate siswa (update)
        Route::put('/{student}', [StudentController::class, 'update'])->middleware('permission:update-student');
        // Route untuk menghapus siswa (delete)
        Route::delete('/{student}', [StudentController::class, 'destroy'])->middleware('permission:delete-student');
        // Route untuk mengembalikan siswa yang dihapus secara soft (restore)
        Route::post('/{student}/restore', [StudentController::class, 'restore'])->middleware('permission:restore-student');
        // Route untuk menghapus siswa secara permanen (forceDelete)
        Route::delete('/{student}/force-delete', [StudentController::class, 'forceDelete'])->middleware('permission:forceDelete-student');
    });

    // --- NEW: Routes untuk Profile Linking ---
    // Route untuk admin/user berwenang untuk menghasilkan token
    // Menggunakan POST karena ini adalah aksi yang mengubah state (membuat token)
    Route::post('link-tokens/generate/{type}/{id}', [ProfileLinkingController::class, 'generateLinkToken']);

    // Route untuk user yang sedang login untuk menautkan akunnya
    // Token diambil dari request body
    Route::post('link-profile', [ProfileLinkingController::class, 'linkProfileAccount']);
});


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
