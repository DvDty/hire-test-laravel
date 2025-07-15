<?php

use App\Http\Controllers\MaintenanceRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/maintenance-requests', [MaintenanceRequestController::class, 'index']);
Route::post('/maintenance-requests', [MaintenanceRequestController::class, 'store']);
