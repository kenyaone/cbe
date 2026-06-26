<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LearnerPortalController;
use App\Http\Controllers\LearnerAuthController;

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
    Route::get('/learn/profile', [LearnerAuthController::class, 'profile'])->name('learner.profile');
    Route::post('/learn/profile', [LearnerAuthController::class, 'updateProfile'])->name('learner.profile.update');
    Route::post('/learn/change-password', [LearnerAuthController::class, 'changePassword'])->name('learner.password.change');
    Route::post('/learn/logout', [LearnerAuthController::class, 'logout'])->name('learner.logout');
});

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
