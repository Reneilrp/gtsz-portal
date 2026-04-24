<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;

/*
 |--------------------------------------------------------------------------
 | API Routes
 |--------------------------------------------------------------------------
 */

// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin & Super Admin Routes
    Route::prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'getStats']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::post('/create-admin', [AdminController::class, 'createAdmin']);
    });

    Route::apiResource('students', StudentController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('sections', SectionController::class);
    Route::apiResource('assignments', AssignmentController::class);
    Route::apiResource('grades', GradeController::class);
    Route::apiResource('attendances', AttendanceController::class);
    Route::apiResource('announcements', AnnouncementController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('school-years', SchoolYearController::class);
});

// NOTE: You do NOT need to write the Login/Register routes here. 
// Laravel Breeze automatically created a separate `routes/auth.php` file for those!