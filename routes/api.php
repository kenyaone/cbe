<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\SyncController;

// Device endpoints
Route::get('/device/initialize', [DeviceController::class, 'initialize']);
Route::post('/device/checkin', [DeviceController::class, 'checkin']);
Route::get('/device/status', [DeviceController::class, 'status']);
Route::get('/device/pending-sync', [DeviceController::class, 'getPendingSync']);

// Cloud sync endpoints
Route::post('/sync/upload', [SyncController::class, 'upload']);
Route::post('/sync/batch-upload', [SyncController::class, 'batchUpload']);
