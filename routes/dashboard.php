<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeController;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeTypeController;
use Modules\Employee\Http\Controllers\Dashboard\V1\AttendanceController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('employee.')->group(function () {
    // Departments AJAX endpoint (must be before resource to avoid {employee} capture)
    Route::get('employees/departments', [EmployeeController::class, 'getDepartments'])
        ->name('employees.departments')
        ->middleware('permission:employees.view_any');

    // Employees - with permission middleware
    Route::middleware('permission:employees.view_any')->group(function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    });

    Route::middleware('permission:employees.view')->group(function () {
        Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('employees/{employee}/qr-badge', [EmployeeController::class, 'qrCode'])->name('employees.qr-badge');
    });

    Route::middleware('permission:employees.create')->group(function () {
        Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    });

    Route::middleware('permission:employees.update')->group(function () {
        Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::patch('employees/{employee}', [EmployeeController::class, 'update']);
        Route::put('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
        Route::post('employees/{employee}/regenerate-qr', [EmployeeController::class, 'regenerateQrCode'])->name('employees.regenerate-qr');
    });

    Route::middleware('permission:employees.delete')->group(function () {
        Route::get('employees/{employee}/delete', [EmployeeController::class, 'confirmDelete'])->name('employees.confirm-delete');
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // Employee Types - with permission middleware
    Route::middleware('permission:employee_types.view_any')->group(function () {
        Route::get('employee-types', [EmployeeTypeController::class, 'index'])->name('employee-types.index');
    });

    Route::middleware('permission:employee_types.view')->group(function () {
        Route::get('employee-types/{employee_type}', [EmployeeTypeController::class, 'show'])->name('employee-types.show');
    });

    Route::middleware('permission:employee_types.create')->group(function () {
        Route::get('employee-types/create', [EmployeeTypeController::class, 'create'])->name('employee-types.create');
        Route::post('employee-types', [EmployeeTypeController::class, 'store'])->name('employee-types.store');
    });

    Route::middleware('permission:employee_types.update')->group(function () {
        Route::get('employee-types/{employee_type}/edit', [EmployeeTypeController::class, 'edit'])->name('employee-types.edit');
        Route::put('employee-types/{employee_type}', [EmployeeTypeController::class, 'update'])->name('employee-types.update');
        Route::patch('employee-types/{employee_type}', [EmployeeTypeController::class, 'update']);
        Route::put('employee-types/{employee_type}/toggle-status', [EmployeeTypeController::class, 'toggleStatus'])->name('employee-types.toggle-status');
    });

    Route::middleware('permission:employee_types.delete')->group(function () {
        Route::get('employee-types/{employee_type}/delete', [EmployeeTypeController::class, 'confirmDelete'])->name('employee-types.confirm-delete');
        Route::delete('employee-types/{employee_type}', [EmployeeTypeController::class, 'destroy'])->name('employee-types.destroy');
    });

    // Attendance - Scanner (available for employees with scan permission)
    Route::middleware('permission:attendances.scan_qr')->group(function () {
        Route::get('attendances/scanner', [AttendanceController::class, 'scanner'])->name('attendances.scanner');
        Route::post('attendances/scan', [AttendanceController::class, 'processScan'])->name('attendances.scan');
        Route::get('attendances/today-summary', [AttendanceController::class, 'todaySummary'])->name('attendances.today-summary');
    });

    // Attendance CRUD
    Route::middleware('permission:attendances.view_any')->group(function () {
        Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    });

    Route::middleware('permission:attendances.view')->group(function () {
        Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    });

    Route::middleware('permission:attendances.create')->group(function () {
        Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
        Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    });

    Route::middleware('permission:attendances.update')->group(function () {
        Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
        Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
        Route::patch('attendances/{attendance}', [AttendanceController::class, 'update']);
    });

    Route::middleware('permission:attendances.delete')->group(function () {
        Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');
    });

    // QR Code Generation
    Route::middleware('permission:employees.view')->group(function () {
        Route::get('employees/{employee}/qr-code', [AttendanceController::class, 'generateEmployeeQr'])->name('employees.qr-code');
    });

    Route::middleware('permission:departments.view')->group(function () {
        Route::get('departments/{department}/qr-code', [AttendanceController::class, 'generateDepartmentQr'])->name('departments.qr-code');
    });

    Route::middleware('permission:classrooms.view')->group(function () {
        Route::get('classrooms/{classroom}/qr-code', [AttendanceController::class, 'generateClassroomQr'])->name('classrooms.qr-code');
    });
});
