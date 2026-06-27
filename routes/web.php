<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LearnerPortalController;
use App\Http\Controllers\LearnerAuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminReportsController;
use App\Http\Controllers\AdminContentUploadController;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\CloudDashboardController;
use App\Http\Controllers\PublicDeviceMapController;

// Admin Authentication
Route::middleware('guest:web')->group(function () {
    Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
});

// Admin Dashboard - Protected Routes
Route::middleware('auth:web', 'is_admin')->group(function () {
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // User Management
    Route::get('/admin/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Learner Management
    Route::get('/admin/learners', [AdminDashboardController::class, 'learners'])->name('admin.learners');

    // Content Management
    Route::get('/admin/content', [AdminDashboardController::class, 'content'])->name('admin.content');

    // Curriculum Management
    Route::get('/admin/curriculum', [AdminDashboardController::class, 'curriculum'])->name('admin.curriculum');

    // Reports
    Route::get('/admin/reports', [AdminReportsController::class, 'index'])->name('admin.reports');
    Route::get('/admin/reports/learner-activity', [AdminReportsController::class, 'learnerActivity'])->name('admin.reports.learner-activity');
    Route::get('/admin/reports/content-stats', [AdminReportsController::class, 'contentStats'])->name('admin.reports.content-stats');
    Route::get('/admin/reports/platform-stats', [AdminReportsController::class, 'platformStats'])->name('admin.reports.platform-stats');

    // Content Upload
    Route::get('/admin/content/upload', [AdminContentUploadController::class, 'create'])->name('admin.content.upload');
    Route::post('/admin/content/upload', [AdminContentUploadController::class, 'store'])->name('admin.content.upload.store');
    Route::get('/admin/content/subjects/{grade}', [AdminContentUploadController::class, 'getSubjects']);
    Route::get('/admin/content/strands/{subject}', [AdminContentUploadController::class, 'getStrands']);
    Route::get('/admin/content/sub-strands/{strand}', [AdminContentUploadController::class, 'getSubStrands']);
});

// Teacher Authentication
Route::middleware('guest:web')->group(function () {
    Route::get('/teacher/login', [TeacherDashboardController::class, 'showLoginForm'])->name('teacher.login');
    Route::post('/teacher/login', [TeacherDashboardController::class, 'teacherLogin'])->name('teacher.login.submit');
});

// Teacher Dashboard - Protected Routes
Route::middleware('auth:web')->group(function () {
    Route::get('/teacher', [TeacherDashboardController::class, 'dashboard'])->name('teacher.dashboard');
    Route::get('/teacher/learner-progress', [TeacherDashboardController::class, 'learnerProgress'])->name('teacher.learner-progress');
    Route::get('/teacher/reports', [TeacherDashboardController::class, 'reports'])->name('teacher.reports');
    Route::post('/teacher/logout', [TeacherDashboardController::class, 'logout'])->name('teacher.logout');
});

// Cloud Dashboard - Admin only
Route::middleware('cloud_auth')->group(function () {
    Route::get('/cloud', [CloudDashboardController::class, 'index'])->name('cloud.dashboard');
    Route::get('/cloud/devices', [CloudDashboardController::class, 'devices'])->name('cloud.devices');
    Route::get('/cloud/device/{deviceId}', [CloudDashboardController::class, 'deviceDetail'])->name('cloud.device-detail');
    Route::get('/cloud/regions', [CloudDashboardController::class, 'regions'])->name('cloud.regions');
    Route::get('/cloud/reports', [CloudDashboardController::class, 'reports'])->name('cloud.reports');
    Route::get('/cloud/api', [CloudDashboardController::class, 'api'])->name('cloud.api');
});

// Login route alias for framework compatibility
Route::redirect('/login', '/learn/login')->name('login');

// Learner Authentication
Route::middleware('guest:learner')->group(function () {
    Route::get('/learn/login', [LearnerAuthController::class, 'showLoginForm'])->name('learner.login');
    Route::post('/learn/login', [LearnerAuthController::class, 'login'])->name('learner.login.submit');
    Route::get('/learn/register', [LearnerAuthController::class, 'showRegisterForm'])->name('learner.register');
    Route::post('/learn/register', [LearnerAuthController::class, 'register'])->name('learner.register.submit');
});

// Learner Portal - Protected routes
Route::middleware('auth.learner')->group(function () {
    Route::get('/learn', [LearnerPortalController::class, 'dashboard'])->name('learner.dashboard');
    Route::get('/learn/grade/{gradeLevel}', [LearnerPortalController::class, 'gradeSubjects'])->name('learner.grade');
    Route::get('/learn/grade/{gradeLevel}/subject/{subjectId}', [LearnerPortalController::class, 'subjectTopics'])->name('learner.subject');
    Route::get('/learn/grade/{gradeLevel}/subject/{subjectId}/topic/{topicId}', [LearnerPortalController::class, 'topicLessons'])->name('learner.topic');
    Route::get('/learn/grade/{gradeLevel}/subject/{subjectId}/topic/{topicId}/lesson/{lessonId}', [LearnerPortalController::class, 'lessonContent'])->name('learner.lesson');

    // Simplified routes (no topics, direct to lessons)
    Route::get('/learn/grade/{gradeLevel}/subject/{subjectId}/lesson/{lessonId}', [LearnerPortalController::class, 'simplifiedLessonContent'])->name('learner.lesson-simple');

    Route::get('/learn/profile', [LearnerAuthController::class, 'profile'])->name('learner.profile');
    Route::post('/learn/profile', [LearnerAuthController::class, 'updateProfile'])->name('learner.profile.update');
    Route::post('/learn/change-password', [LearnerAuthController::class, 'changePassword'])->name('learner.password.change');
    Route::post('/learn/logout', [LearnerAuthController::class, 'logout'])->name('learner.logout');
});

// Public Device Map - No authentication required
Route::get('/devices', [PublicDeviceMapController::class, 'map'])->name('public.device-map');
Route::get('/devices/api', [PublicDeviceMapController::class, 'api'])->name('public.devices-api');
Route::get('/devices/status', [PublicDeviceMapController::class, 'status'])->name('public.device-status');
Route::get('/devices/embed', [PublicDeviceMapController::class, 'embed'])->name('public.device-map-embed');

// Admin/Curriculum Browser (original)
Route::get('/', [CurriculumController::class, 'index'])->name('curriculum.index');
Route::get('/curriculum/{type}', [CurriculumController::class, 'showType'])->name('curriculum.type');
Route::get('/curriculum/{type}/{area}', [CurriculumController::class, 'showArea'])->name('curriculum.area');
Route::get('/curriculum/{type}/{area}/{strand}', [CurriculumController::class, 'showStrand'])->name('curriculum.strand');
Route::get('/curriculum/{type}/{area}/{strand}/{subStrand}', [CurriculumController::class, 'showSubStrand'])->name('curriculum.sub-strand');

// Media streaming routes
Route::get('/stream/{filePath}', [VideoStreamController::class, 'stream'])->name('stream.video');
Route::get('/interactive/{filePath}', [ContentController::class, 'serveInteractive'])->name('serve.interactive');
Route::get('/pdf/{filePath}', [ContentController::class, 'servePdf'])->name('serve.pdf');
