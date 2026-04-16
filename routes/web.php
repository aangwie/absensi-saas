<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\MobileController;
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

/*
|--------------------------------------------------------------------------
| Mobile Attendance Routes (Chrome Mobile)
|--------------------------------------------------------------------------
*/
Route::prefix('m')->group(function () {
    Route::get('/login', [MobileController::class, 'loginPage'])->name('mobile.login');
    Route::post('/login', [MobileController::class, 'login'])->name('mobile.login.submit');
    Route::get('/dashboard', [MobileController::class, 'dashboard'])->name('mobile.dashboard');
    Route::post('/logout', [MobileController::class, 'logout'])->name('mobile.logout');
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
    Route::post('students/{student}/verify', [StudentController::class, 'verify'])->name('students.verify');
    Route::post('students/{student}/reject', [StudentController::class, 'reject'])->name('students.reject');

    // Teachers
    Route::resource('teachers', TeacherController::class);
    Route::post('teachers/{teacher}/verify', [TeacherController::class, 'verify'])->name('teachers.verify');
    Route::post('teachers/{teacher}/reject', [TeacherController::class, 'reject'])->name('teachers.reject');

    // Attendances
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    // Users (admin & super_admin)
    Route::resource('users', UserController::class)->middleware('role:super_admin,admin');

    // Settings (super_admin only)
    Route::prefix('settings')->name('settings.')->middleware('role:super_admin')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/update-token', [SettingController::class, 'updateToken'])->name('update-token');
        Route::post('/git-pull', [SettingController::class, 'gitPull'])->name('git-pull');
        Route::post('/clear-cache', [SettingController::class, 'clearCache'])->name('clear-cache');
        Route::post('/clear-config', [SettingController::class, 'clearConfig'])->name('clear-config');
        Route::post('/migrate', [SettingController::class, 'migrate'])->name('migrate');
    });
});
