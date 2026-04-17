<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

// ============= ADMIN ROUTES =============
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\Admin\DashboardController::class, 'stats']);
    Route::apiResource('users', App\Http\Controllers\Api\Admin\UserController::class);
    Route::apiResource('students', App\Http\Controllers\Api\Admin\StudentController::class);
    Route::apiResource('teachers', App\Http\Controllers\Api\Admin\TeacherController::class);
});

// ============= COMPANIES =============
Route::middleware('auth:sanctum')->apiResource('companies', App\Http\Controllers\Api\Admin\CompanyController::class);

// ============= SISWA ROUTES =============
Route::prefix('siswa')->middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\Siswa\DashboardController::class, 'stats']);
    Route::post('/attendance/check-in', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'checkOut']);
    Route::get('/attendance/today', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'today']);
    Route::get('/attendance/history', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'history']);
    Route::apiResource('logbooks', App\Http\Controllers\Api\Siswa\LogbookController::class);
      Route::get('/report', [App\Http\Controllers\Api\Siswa\ReportController::class, 'index']);
    Route::post('/report/upload', [App\Http\Controllers\Api\Siswa\ReportController::class, 'upload']);
    Route::delete('/report/delete', [App\Http\Controllers\Api\Siswa\ReportController::class, 'delete']);
});