<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('admin.dashboard') : redirect()->route('login');
});

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin Panel Routes (protected)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'tenant'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Schools (super_admin only)
    Route::resource('schools', SchoolController::class)->middleware('role:super_admin');

    // Locations
    Route::resource('locations', LocationController::class);

    // Students
    Route::resource('students', StudentController::class);

    // Teachers
    Route::resource('teachers', TeacherController::class);

    // Attendances
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    // Users (admin & super_admin)
    Route::resource('users', UserController::class)->middleware('role:super_admin,admin');
});
