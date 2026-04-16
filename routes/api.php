<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/student/login', [AuthController::class, 'studentLogin']);
    Route::post('/teacher/login', [AuthController::class, 'teacherLogin']);
});

// Protected routes (requires Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);

    // Attendance
    Route::prefix('attendance')->group(function () {
        Route::post('/check-in', [AttendanceApiController::class, 'checkIn']);
        Route::post('/check-out', [AttendanceApiController::class, 'checkOut']);
        Route::get('/history', [AttendanceApiController::class, 'history']);
        Route::get('/today', [AttendanceApiController::class, 'todayStatus']);
    });

    // School Locations & Schedule
    Route::get('/school/locations', [AttendanceApiController::class, 'schoolLocations']);
    Route::get('/school/schedule', [AttendanceApiController::class, 'todaySchedule']);
});
