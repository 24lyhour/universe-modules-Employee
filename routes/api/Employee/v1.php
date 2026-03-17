<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Api\V1\Employee\EmployeeAuthController;

/*
|--------------------------------------------------------------------------
| Public Routes (no authentication required)
|--------------------------------------------------------------------------
| These routes are for employee mobile app login
*/
Route::prefix('v1/employee')->group(function () {
    // Auth
    Route::post('auth/login', [EmployeeAuthController::class, 'login'])
        ->name('employee.auth.login');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (requires authentication)
|--------------------------------------------------------------------------
| These routes require employee to be authenticated
*/
Route::middleware(['auth:sanctum'])->prefix('v1/employee')->group(function () {
    // Auth
    Route::post('auth/logout', [EmployeeAuthController::class, 'logout'])
        ->name('employee.auth.logout');
    Route::post('auth/logout-all', [EmployeeAuthController::class, 'logoutAll'])
        ->name('employee.auth.logout-all');
    Route::get('auth/me', [EmployeeAuthController::class, 'me'])
        ->name('employee.auth.me');
});
