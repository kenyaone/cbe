<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\VideoStreamController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\LearnerPortalController;

// Learner Portal - organized by grade level
Route::get('/learn', [LearnerPortalController::class, 'dashboard'])->name('learner.dashboard');
Route::get('/learn/grade/{gradeLevel}', [LearnerPortalController::class, 'gradeSubjects'])->name('learner.grade');
Route::get('/learn/grade/{gradeLevel}/subject/{subjectId}', [LearnerPortalController::class, 'subjectTopics'])->name('learner.subject');
Route::get('/learn/grade/{gradeLevel}/subject/{subjectId}/topic/{topicId}', [LearnerPortalController::class, 'topicLessons'])->name('learner.topic');
Route::get('/learn/grade/{gradeLevel}/subject/{subjectId}/topic/{topicId}/lesson/{lessonId}', [LearnerPortalController::class, 'lessonContent'])->name('learner.lesson');

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
