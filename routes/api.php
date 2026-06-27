<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DeviceController;

Route::get('/device/initialize', [DeviceController::class, 'initialize']);
Route::post('/device/checkin', [DeviceController::class, 'checkin']);
Route::get('/device/status', [DeviceController::class, 'status']);
Route::get('/device/pending-sync', [DeviceController::class, 'getPendingSync']);
