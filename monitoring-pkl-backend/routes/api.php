<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AssessmentController;

// ================================================================
// ===================== AUTHENTICATION ROUTES ===================
// ================================================================
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

// ================================================================
// ===================== PUBLIC REGISTRATION ROUTES ==============
// ================================================================
Route::prefix('register')->group(function () {
    Route::post('/siswa', [RegisterController::class, 'registerSiswa']);
    Route::post('/guru', [RegisterController::class, 'registerGuru']);
    Route::post('/perusahaan', [RegisterController::class, 'registerPerusahaan']);
});

// ================================================================
// ===================== ADMIN ROUTES ============================
// ================================================================
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {


     Route::get('/students/assessments', [App\Http\Controllers\Api\Admin\AssessmentController::class, 'getStudentsForAssessment']);
    Route::post('/assessments/{id}', [App\Http\Controllers\Api\Admin\AssessmentController::class, 'store']);
    
    // ---------------------- DASHBOARD --------------------------
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [App\Http\Controllers\Api\Admin\DashboardController::class, 'stats']);
        Route::get('/all', [App\Http\Controllers\Api\Admin\DashboardController::class, 'getAllData']);
        Route::get('/recent-activities', [App\Http\Controllers\Api\Admin\DashboardController::class, 'recentActivities']);
        Route::get('/top-students', [App\Http\Controllers\Api\Admin\DashboardController::class, 'topStudents']);
    });
    Route::get('/attendance/stats', [App\Http\Controllers\Api\Admin\DashboardController::class, 'attendanceStats']);
    
    // ---------------------- USER MANAGEMENT --------------------
    Route::apiResource('users', App\Http\Controllers\Api\Admin\UserController::class);
    Route::apiResource('students', App\Http\Controllers\Api\Admin\StudentController::class);
    Route::apiResource('teachers', App\Http\Controllers\Api\Admin\TeacherController::class);
    Route::apiResource('roles', App\Http\Controllers\Api\Admin\RoleController::class);
    Route::get('/teachers/options', [App\Http\Controllers\Api\Admin\TeacherController::class, 'options']);
    Route::get('/students/stats', [App\Http\Controllers\Api\Admin\StudentController::class, 'stats']);
    
    // ---------------------- COMPANY MANAGEMENT -----------------
    Route::apiResource('companies', App\Http\Controllers\Api\Admin\CompanyController::class);
    
    // ---------------------- CLASS MANAGEMENT -------------------
    Route::apiResource('classes', App\Http\Controllers\Api\Admin\ClassController::class);
    
    // ---------------------- PLACEMENT MANAGEMENT ----------------
    Route::apiResource('placements', App\Http\Controllers\Api\Admin\PlacementController::class);
    
    // ---------------------- REGISTRATION MANAGEMENT ------------
    Route::prefix('registrations')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\RegistrationController::class, 'index']);
        Route::get('/history', [App\Http\Controllers\Api\Admin\RegistrationController::class, 'history']);
        Route::post('/{id}/approve', [App\Http\Controllers\Api\Admin\RegistrationController::class, 'approve']);
        Route::post('/{id}/reject', [App\Http\Controllers\Api\Admin\RegistrationController::class, 'reject']);
    });
    
    // ---------------------- MONITORING -------------------------
    Route::prefix('monitoring')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'index']);
        Route::get('/attendance', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'attendance']);
        Route::get('/logbook', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'logbook']);
        Route::get('/progress', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'progress']);
        Route::get('/map', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'map']);
    });
    Route::get('/students/{id}', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'show']);
    
    // ---------------------- MAPS -------------------------------
    Route::prefix('map')->group(function () {
        Route::get('/data', [App\Http\Controllers\Api\Admin\MapController::class, 'getMapData']);
        Route::get('/companies', [App\Http\Controllers\Api\Admin\MapController::class, 'getCompanyLocations']);
    });
    
    // ---------------------- REPORTS ----------------------------
    Route::prefix('reports')->group(function () {
        // Download Excel (Blob)
        Route::get('/attendance', [App\Http\Controllers\Api\Admin\ReportController::class, 'attendance']);
        Route::get('/logbook', [App\Http\Controllers\Api\Admin\ReportController::class, 'logbook']);
        Route::get('/summary', [App\Http\Controllers\Api\Admin\ReportController::class, 'summary']);
        Route::get('/grade', [App\Http\Controllers\Api\Admin\ReportController::class, 'grade']);
        
        // Preview JSON
        Route::get('/attendance/json', [App\Http\Controllers\Api\Admin\ReportController::class, 'attendanceJson']);
        Route::get('/logbook/json', [App\Http\Controllers\Api\Admin\ReportController::class, 'logbookJson']);
        Route::get('/summary/json', [App\Http\Controllers\Api\Admin\ReportController::class, 'summaryJson']);
        Route::get('/grade/json', [App\Http\Controllers\Api\Admin\ReportController::class, 'gradeJson']);
    });
    
    // ---------------------- ASSESSMENTS ------------------------
    Route::prefix('assessments')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Admin\AssessmentController::class, 'index']);
        Route::post('/{id}', [App\Http\Controllers\Api\Admin\AssessmentController::class, 'store']);
        Route::get('/report', [App\Http\Controllers\Api\Admin\AssessmentController::class, 'report']);
    });
    
    // ---------------------- SETTINGS ---------------------------
    Route::prefix('settings')->group(function () {
        Route::get('/general', [App\Http\Controllers\Api\Admin\SettingController::class, 'general']);
        Route::post('/general', [App\Http\Controllers\Api\Admin\SettingController::class, 'updateGeneral']);
        Route::get('/school', [App\Http\Controllers\Api\Admin\SettingController::class, 'school']);
        Route::post('/school', [App\Http\Controllers\Api\Admin\SettingController::class, 'updateSchool']);
        Route::get('/academic', [App\Http\Controllers\Api\Admin\SettingController::class, 'academic']);
        Route::post('/academic', [App\Http\Controllers\Api\Admin\SettingController::class, 'updateAcademic']);
        Route::get('/radius', [App\Http\Controllers\Api\Admin\SettingController::class, 'radius']);
        Route::post('/radius', [App\Http\Controllers\Api\Admin\SettingController::class, 'updateRadius']);
    });
});

// ================================================================
// ===================== PUBLIC COMPANIES ROUTES =================
// ================================================================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/companies', [App\Http\Controllers\Api\Admin\CompanyController::class, 'index']);
    Route::get('/companies/{id}', [App\Http\Controllers\Api\Admin\CompanyController::class, 'show']);
});

// ================================================================
// ===================== GURU ROUTES =============================
// ================================================================
Route::prefix('guru')->middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [App\Http\Controllers\Api\Guru\DashboardController::class, 'stats']);
        Route::get('/top-students', [App\Http\Controllers\Api\Guru\DashboardController::class, 'topStudents']);
        Route::get('/attendance/chart', [App\Http\Controllers\Api\Guru\DashboardController::class, 'attendanceChart']);
    });
    
    // Monitoring
    Route::prefix('monitoring')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'show']);
    });
    Route::get('/students', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'students']);
    Route::get('/students/{id}', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'show']);
    
    // Logbook Review
    Route::prefix('logbooks')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Guru\ReviewController::class, 'index']);
        Route::get('/pending', [App\Http\Controllers\Api\Guru\ReviewController::class, 'pending']);
        Route::get('/{id}', [App\Http\Controllers\Api\Guru\ReviewController::class, 'show']);
        Route::put('/{id}/review', [App\Http\Controllers\Api\Guru\ReviewController::class, 'review']);
        Route::put('/{id}/approve', [App\Http\Controllers\Api\Guru\ReviewController::class, 'approve']);
        Route::put('/{id}/reject', [App\Http\Controllers\Api\Guru\ReviewController::class, 'reject']);
    });
    
    // Permission Approval
    Route::prefix('permissions')->group(function () {
        Route::get('/pending', [App\Http\Controllers\Api\Guru\PermissionController::class, 'pending']);
        Route::get('/history', [App\Http\Controllers\Api\Guru\PermissionController::class, 'history']);
        Route::put('/{id}/approve', [App\Http\Controllers\Api\Guru\PermissionController::class, 'approve']);
        Route::put('/{id}/reject', [App\Http\Controllers\Api\Guru\PermissionController::class, 'reject']);
    });
    
    // Assessments
    Route::prefix('assessments')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Guru\AssessmentController::class, 'index']);
        Route::post('/', [App\Http\Controllers\Api\Guru\AssessmentController::class, 'store']);
        Route::put('/{id}', [App\Http\Controllers\Api\Guru\AssessmentController::class, 'update']);
    });
    
    // Attendance Monitoring
    Route::prefix('attendances')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Guru\AttendanceController::class, 'index']);
        Route::get('/summary', [App\Http\Controllers\Api\Guru\AttendanceController::class, 'summary']);
        Route::get('/students', [App\Http\Controllers\Api\Guru\AttendanceController::class, 'students']);
    });
    Route::get('/attendance-summary', [App\Http\Controllers\Api\Guru\AttendanceSummaryController::class, 'index']);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/attendance', [App\Http\Controllers\Api\Guru\ReportController::class, 'attendance']);
        Route::get('/logbook', [App\Http\Controllers\Api\Guru\ReportController::class, 'logbook']);
        Route::get('/assessment', [App\Http\Controllers\Api\Guru\ReportController::class, 'assessment']);
        Route::get('/pending', [App\Http\Controllers\Api\Guru\ReportReviewController::class, 'index']);
        Route::put('/{id}/approve', [App\Http\Controllers\Api\Guru\ReportReviewController::class, 'approve']);
        Route::put('/{id}/reject', [App\Http\Controllers\Api\Guru\ReportReviewController::class, 'reject']);
    });
});

// ================================================================
// ===================== SISWA ROUTES ============================
// ================================================================
Route::prefix('siswa')->middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [App\Http\Controllers\Api\Siswa\DashboardController::class, 'stats']);
        Route::get('/logbooks/recent', [App\Http\Controllers\Api\Siswa\LogbookController::class, 'recent']);
    });
    
    // Attendance
    Route::prefix('attendance')->group(function () {
        Route::post('/check-in', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'checkIn']);
        Route::post('/check-out', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'checkOut']);
        Route::post('/photo', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'uploadPhoto']);
        Route::get('/today', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'today']);
        Route::get('/history', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'history']);
        Route::get('/monthly', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'monthly']);
    });
    
    // Logbook
    Route::apiResource('logbooks', App\Http\Controllers\Api\Siswa\LogbookController::class);
    
    // Permission
    Route::apiResource('permissions', App\Http\Controllers\Api\Siswa\PermissionController::class);
    
    // Report (Laporan PKL)
    Route::prefix('report')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Siswa\ReportController::class, 'index']);
        Route::post('/upload', [App\Http\Controllers\Api\Siswa\ReportController::class, 'upload']);
        Route::delete('/delete', [App\Http\Controllers\Api\Siswa\ReportController::class, 'delete']);
    });
    
    // Final Report
    Route::apiResource('final-reports', App\Http\Controllers\Api\Siswa\FinalReportController::class);
    
    // Company Info
    Route::prefix('company')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Siswa\CompanyController::class, 'index']);
        Route::get('/location', [App\Http\Controllers\Api\Siswa\CompanyController::class, 'location']);
    });
});

// ================================================================
// ===================== PERUSAHAAN ROUTES =======================
// ================================================================
Route::prefix('perusahaan')->middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [App\Http\Controllers\Api\Perusahaan\DashboardController::class, 'stats']);
        Route::get('/top-students', [App\Http\Controllers\Api\Perusahaan\DashboardController::class, 'topStudents']);
    });
    
    // Logbook Assessment
    Route::prefix('logbooks')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'index']);
        Route::get('/pending', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'pending']);
        Route::get('/{id}', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'show']);
        Route::put('/{id}/grade', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'grade']);
        Route::post('/{id}/feedback', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'feedback']);
    });
    
    // Student Progress
    Route::prefix('progress')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Perusahaan\ProgressController::class, 'index']);
        Route::get('/{id}', [App\Http\Controllers\Api\Perusahaan\ProgressController::class, 'show']);
        Route::get('/report', [App\Http\Controllers\Api\Perusahaan\ProgressController::class, 'report']);
    });
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/logbook', [App\Http\Controllers\Api\Perusahaan\ReportController::class, 'logbook']);
        Route::get('/progress', [App\Http\Controllers\Api\Perusahaan\ReportController::class, 'progress']);
    });
});

// ================================================================
// ===================== NOTIFICATIONS ROUTES ====================
// ================================================================
Route::prefix('notifications')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
    Route::delete('/clear', [NotificationController::class, 'clearAll']);
});

// ================================================================
// ===================== ASSESSMENTS ROUTES ======================
// ================================================================
Route::prefix('assessments')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [AssessmentController::class, 'index']);
    Route::get('/my', [AssessmentController::class, 'myAssessments']);
    Route::post('/', [AssessmentController::class, 'store']);
    Route::get('/{id}', [AssessmentController::class, 'show']);
    Route::put('/{id}', [AssessmentController::class, 'update']);
    Route::delete('/{id}', [AssessmentController::class, 'destroy']);
});

// ================================================================
// ===================== PROFILE ROUTES ==========================
// ================================================================
Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::put('/', [ProfileController::class, 'update']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);
    Route::post('/photo', [ProfileController::class, 'uploadPhoto']);
    Route::delete('/photo', [ProfileController::class, 'deletePhoto']);
});

// ================================================================
// ===================== FALLBACK ROUTE ==========================
// ================================================================
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found',
        'status' => 404
    ], 404);
});