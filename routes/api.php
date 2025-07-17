<?php

use App\Http\Controllers\MaintenanceRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/maintenance-requests', [MaintenanceRequestController::class, 'index'])->name('maintenance-requests.index');
Route::post('/maintenance-requests', [MaintenanceRequestController::class, 'store'])->name('maintenance-requests.store');
