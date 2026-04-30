<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\Siswa\CompanyInfoController;

// ================================================================
// ===================== AUTHENTICATION ROUTES ===================
// ================================================================
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);

// ================================================================
// ===================== PUBLIC REGISTRATION ROUTES ===============
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
    
    // ---------------------- DASHBOARD --------------------------
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\Admin\DashboardController::class, 'stats']);
    Route::get('/dashboard/all', [App\Http\Controllers\Api\Admin\DashboardController::class, 'getAllData']);
    Route::get('/attendance/stats', [App\Http\Controllers\Api\Admin\DashboardController::class, 'attendanceStats']);
    Route::get('/recent-activities', [App\Http\Controllers\Api\Admin\DashboardController::class, 'recentActivities']);
    Route::get('/top-students', [App\Http\Controllers\Api\Admin\DashboardController::class, 'topStudents']);
    
    // ---------------------- USER MANAGEMENT --------------------
    Route::apiResource('users', App\Http\Controllers\Api\Admin\UserController::class);
    Route::apiResource('students', App\Http\Controllers\Api\Admin\StudentController::class);
    Route::apiResource('teachers', App\Http\Controllers\Api\Admin\TeacherController::class);
    Route::apiResource('roles', App\Http\Controllers\Api\Admin\RoleController::class);
    
    // ---------------------- COMPANY MANAGEMENT -----------------
    // PERBAIKAN: Gunakan resource agar semua method (GET, POST, PUT, DELETE) tersedia
    Route::apiResource('companies', App\Http\Controllers\Api\Admin\CompanyController::class);
    
    // ---------------------- CLASS MANAGEMENT -------------------
    Route::apiResource('classes', App\Http\Controllers\Api\Admin\ClassController::class);
    
    // ---------------------- PLACEMENT MANAGEMENT ----------------
    Route::apiResource('placements', App\Http\Controllers\Api\Admin\PlacementController::class);
    
    // ---------------------- REGISTRATION MANAGEMENT ------------
    Route::get('/registrations', [App\Http\Controllers\Api\Admin\RegistrationController::class, 'index']);
    Route::get('/registrations/history', [App\Http\Controllers\Api\Admin\RegistrationController::class, 'history']);
    Route::post('/registrations/{id}/approve', [App\Http\Controllers\Api\Admin\RegistrationController::class, 'approve']);
    Route::post('/registrations/{id}/reject', [App\Http\Controllers\Api\Admin\RegistrationController::class, 'reject']);
    
    // ---------------------- MONITORING -------------------------
    Route::get('/monitoring', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'index']);
    Route::get('/monitoring/attendance', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'attendance']);
    Route::get('/monitoring/logbook', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'logbook']);
    Route::get('/monitoring/progress', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'progress']);
    Route::get('/monitoring/map', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'map']);
    Route::get('/students/{id}', [App\Http\Controllers\Api\Admin\MonitoringController::class, 'show']);
    
    // ---------------------- MAPS -------------------------------
    Route::get('/map/data', [App\Http\Controllers\Api\Admin\MapController::class, 'getMapData']);
    Route::get('/map/companies', [App\Http\Controllers\Api\Admin\MapController::class, 'getCompanyLocations']);
    
    // ---------------------- REPORTS ----------------------------
    Route::get('/reports/attendance', [App\Http\Controllers\Api\Admin\ReportController::class, 'attendance']);
    Route::get('/reports/logbook', [App\Http\Controllers\Api\Admin\ReportController::class, 'logbook']);
    Route::get('/reports/summary', [App\Http\Controllers\Api\Admin\ReportController::class, 'summary']);
    Route::get('/reports/grade', [App\Http\Controllers\Api\Admin\ReportController::class, 'grade']);
    
    // ---------------------- ASSESSMENTS ------------------------
    Route::get('/assessments', [App\Http\Controllers\Api\Admin\AssessmentController::class, 'index']);
    Route::post('/assessments/{id}', [App\Http\Controllers\Api\Admin\AssessmentController::class, 'store']);
    Route::get('/assessments/report', [App\Http\Controllers\Api\Admin\AssessmentController::class, 'report']);
    
    // ---------------------- SETTINGS ---------------------------
    Route::get('/settings/general', [App\Http\Controllers\Api\Admin\SettingController::class, 'general']);
    Route::post('/settings/general', [App\Http\Controllers\Api\Admin\SettingController::class, 'updateGeneral']);
    Route::get('/settings/school', [App\Http\Controllers\Api\Admin\SettingController::class, 'school']);
    Route::post('/settings/school', [App\Http\Controllers\Api\Admin\SettingController::class, 'updateSchool']);
    Route::get('/settings/academic', [App\Http\Controllers\Api\Admin\SettingController::class, 'academic']);
    Route::post('/settings/academic', [App\Http\Controllers\Api\Admin\SettingController::class, 'updateAcademic']);
    Route::get('/settings/radius', [App\Http\Controllers\Api\Admin\SettingController::class, 'radius']);
    Route::post('/settings/radius', [App\Http\Controllers\Api\Admin\SettingController::class, 'updateRadius']);

    Route::get('/admin/students/stats', [App\Http\Controllers\Api\Admin\StudentController::class, 'stats']);
});

// ================================================================
// ===================== PUBLIC COMPANIES ROUTES (untuk semua role) =====
// ================================================================
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/companies', [App\Http\Controllers\Api\Admin\CompanyController::class, 'index']);
    Route::get('/companies/{id}', [App\Http\Controllers\Api\Admin\CompanyController::class, 'show']);
});

// ================================================================
// ===================== GURU ROUTES ============================
// ================================================================
Route::prefix('guru')->middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\Guru\DashboardController::class, 'stats']);
    Route::get('/top-students', [App\Http\Controllers\Api\Guru\DashboardController::class, 'topStudents']);
    Route::get('/attendance/chart', [App\Http\Controllers\Api\Guru\DashboardController::class, 'attendanceChart']);


     // MONITORING SISWA - TAMBAHKAN INI
    Route::get('/monitoring', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'index']);
    Route::get('/monitoring/{id}', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'show']);
    
    // Monitoring
     Route::get('/monitoring', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'index']);
    Route::get('/students', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'students']);
    Route::get('/students/{id}', [App\Http\Controllers\Api\Guru\MonitoringController::class, 'show']);
    
    // Logbook Review
    Route::get('/logbooks', [App\Http\Controllers\Api\Guru\ReviewController::class, 'index']);
    Route::get('/logbooks/pending', [App\Http\Controllers\Api\Guru\ReviewController::class, 'pending']);
    Route::get('/logbooks/{id}', [App\Http\Controllers\Api\Guru\ReviewController::class, 'show']);
    Route::put('/logbooks/{id}/review', [App\Http\Controllers\Api\Guru\ReviewController::class, 'review']);
    Route::put('/logbooks/{id}/approve', [App\Http\Controllers\Api\Guru\ReviewController::class, 'approve']);
    Route::put('/logbooks/{id}/reject', [App\Http\Controllers\Api\Guru\ReviewController::class, 'reject']);
    
    // Permission Approval
    Route::get('/permissions/pending', [App\Http\Controllers\Api\Guru\PermissionController::class, 'pending']);
    Route::get('/permissions/history', [App\Http\Controllers\Api\Guru\PermissionController::class, 'history']);
    Route::put('/permissions/{id}/approve', [App\Http\Controllers\Api\Guru\PermissionController::class, 'approve']);
    Route::put('/permissions/{id}/reject', [App\Http\Controllers\Api\Guru\PermissionController::class, 'reject']);
    
    // Assessments
    Route::get('/assessments', [App\Http\Controllers\Api\Guru\AssessmentController::class, 'index']);
    Route::post('/assessments', [App\Http\Controllers\Api\Guru\AssessmentController::class, 'store']);
    Route::put('/assessments/{id}', [App\Http\Controllers\Api\Guru\AssessmentController::class, 'update']);

    // Attendance Monitoring
    Route::get('/attendances', [App\Http\Controllers\Api\Guru\AttendanceController::class, 'index']);
Route::get('/attendances/summary', [App\Http\Controllers\Api\Guru\AttendanceController::class, 'summary']);
 Route::get('/students', [App\Http\Controllers\Api\Guru\AttendanceController::class, 'students']);
 Route::get('/attendance-summary', [App\Http\Controllers\Api\Guru\AttendanceSummaryController::class, 'index']);
    
    // Reports
    Route::get('/reports/attendance', [App\Http\Controllers\Api\Guru\ReportController::class, 'attendance']);
    Route::get('/reports/logbook', [App\Http\Controllers\Api\Guru\ReportController::class, 'logbook']);
    Route::get('/reports/assessment', [App\Http\Controllers\Api\Guru\ReportController::class, 'assessment']);
    Route::get('/reports/pending', [App\Http\Controllers\Api\Guru\ReportReviewController::class, 'index']);
    Route::put('/reports/{id}/approve', [App\Http\Controllers\Api\Guru\ReportReviewController::class, 'approve']);
    Route::put('/reports/{id}/reject', [App\Http\Controllers\Api\Guru\ReportReviewController::class, 'reject']);
});

// ================================================================
// ===================== SISWA ROUTES ===========================
// ================================================================
// ================================================================
// ===================== SISWA ROUTES ===========================
// ================================================================
Route::prefix('siswa')->middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\Siswa\DashboardController::class, 'stats']);
    Route::get('/logbooks/recent', [App\Http\Controllers\Api\Siswa\LogbookController::class, 'recent']);
    
    // Attendance
    Route::post('/attendance/check-in', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'checkOut']);
    Route::post('/attendance/photo', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'uploadPhoto']);
    Route::get('/attendance/today', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'today']);
    Route::get('/attendance/history', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'history']);
    Route::get('/attendance/monthly', [App\Http\Controllers\Api\Siswa\AttendanceController::class, 'monthly']);
    
    // Logbook
    Route::apiResource('logbooks', App\Http\Controllers\Api\Siswa\LogbookController::class);
    
    // PERMISSION - PERBAIKI INI
    Route::get('/permissions', [App\Http\Controllers\Api\Siswa\PermissionController::class, 'index']);
    Route::post('/permissions', [App\Http\Controllers\Api\Siswa\PermissionController::class, 'store']);
    Route::get('/permissions/{id}', [App\Http\Controllers\Api\Siswa\PermissionController::class, 'show']);
    Route::put('/permissions/{id}', [App\Http\Controllers\Api\Siswa\PermissionController::class, 'update']);
    Route::delete('/permissions/{id}', [App\Http\Controllers\Api\Siswa\PermissionController::class, 'destroy']);
    
    // Report (Laporan PKL)
    Route::get('/report', [App\Http\Controllers\Api\Siswa\ReportController::class, 'index']);
    Route::post('/report/upload', [App\Http\Controllers\Api\Siswa\ReportController::class, 'upload']);
    Route::delete('/report/delete', [App\Http\Controllers\Api\Siswa\ReportController::class, 'delete']);
    
    // Final Report
    Route::apiResource('final-reports', App\Http\Controllers\Api\Siswa\FinalReportController::class);
    
    // Company Info
    Route::get('/company', [App\Http\Controllers\Api\Siswa\CompanyController::class, 'index']);
    Route::get('/company/location', [App\Http\Controllers\Api\Siswa\CompanyController::class, 'location']);
});

// ================================================================
// ===================== PERUSAHAAN ROUTES =======================
// ================================================================
Route::prefix('perusahaan')->middleware('auth:sanctum')->group(function () {
    
    // Dashboard
    Route::get('/dashboard/stats', [App\Http\Controllers\Api\Perusahaan\DashboardController::class, 'stats']);
    Route::get('/top-students', [App\Http\Controllers\Api\Perusahaan\DashboardController::class, 'topStudents']);
    
    // Logbook Assessment
    Route::get('/logbooks', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'index']);
    Route::get('/logbooks/pending', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'pending']);
    Route::get('/logbooks/{id}', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'show']);
    Route::put('/logbooks/{id}/grade', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'grade']);
    Route::post('/logbooks/{id}/feedback', [App\Http\Controllers\Api\Perusahaan\AssessmentController::class, 'feedback']);
    
    // Student Progress
    Route::get('/progress', [App\Http\Controllers\Api\Perusahaan\ProgressController::class, 'index']);
    Route::get('/progress/{id}', [App\Http\Controllers\Api\Perusahaan\ProgressController::class, 'show']);
    Route::get('/progress/report', [App\Http\Controllers\Api\Perusahaan\ProgressController::class, 'report']);
    
    // Reports
    Route::get('/reports/logbook', [App\Http\Controllers\Api\Perusahaan\ReportController::class, 'logbook']);
    Route::get('/reports/progress', [App\Http\Controllers\Api\Perusahaan\ReportController::class, 'progress']);
});

// ================================================================
// ===================== NOTIFICATIONS ==========================
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
// ===================== ASSESSMENTS ============================
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
// ===================== PROFILE ROUTES =========================
// ================================================================
Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index']);
    Route::put('/', [ProfileController::class, 'update']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);
    Route::post('/photo', [ProfileController::class, 'uploadPhoto']);
    Route::delete('/photo', [ProfileController::class, 'deletePhoto']);
});

// ================================================================
// ===================== FALLBACK ROUTE =========================
// ================================================================
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route not found',
        'status' => 404
    ], 404);
});