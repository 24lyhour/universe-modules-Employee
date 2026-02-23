<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Dashboard\V1\AttendanceController;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeController;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeTypeController;

Route::middleware(['auth', 'verified'])->prefix('employee')->name('employee.')->group(function () {
    // Employee resource routes
    Route::resource('employees', EmployeeController::class);

    // Employee additional routes
    Route::get('employees/{employee}/qr-code', [EmployeeController::class, 'qrCode'])->name('employees.qr-code');
    Route::post('employees/{employee}/regenerate-qr', [EmployeeController::class, 'regenerateQrCode'])->name('employees.regenerate-qr');
    Route::post('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::get('employees/{employee}/confirm-delete', [EmployeeController::class, 'confirmDelete'])->name('employees.confirm-delete');
    Route::get('departments', [EmployeeController::class, 'getDepartments'])->name('departments');

    // Employee types
    Route::resource('types', EmployeeTypeController::class)->names('types');

    // Attendance routes
    Route::resource('attendances', AttendanceController::class);
});
