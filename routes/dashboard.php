<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeController;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeTypeController;
use Modules\Employee\Http\Controllers\Dashboard\V1\AttendanceController;

/*
|--------------------------------------------------------------------------
| Employee Module Dashboard Routes
|--------------------------------------------------------------------------
|
| Using 'auto.permission' middleware which automatically resolves permissions
| from route names. Route naming pattern: {resource}.{action}
|
| Permission mapping:
| - index -> view_any
| - create/store -> create
| - show -> view
| - edit/update -> update
| - destroy/delete -> delete
|
| For non-standard actions, use explicit permission middleware.
|
*/

Route::middleware(['auth', 'verified', 'auto.permission'])->prefix('dashboard')->name('employee.')->group(function () {

    // Departments AJAX endpoint
    Route::get('employees/departments', [EmployeeController::class, 'getDepartments'])->name('employees.departments');

    // Employees - CREATE routes first (before parameterized routes)
    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('employees/{employee}/qr-badge', [EmployeeController::class, 'qrCode'])->name('employees.qr-badge');
    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::patch('employees/{employee}', [EmployeeController::class, 'update']);
    Route::put('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
    Route::post('employees/{employee}/regenerate-qr', [EmployeeController::class, 'regenerateQrCode'])->name('employees.regenerate-qr');
    Route::get('employees/{employee}/delete', [EmployeeController::class, 'confirmDelete'])->name('employees.confirm-delete');
    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Employee Types - CREATE routes first
    Route::get('employee-types/create', [EmployeeTypeController::class, 'create'])->name('employee-types.create');
    Route::post('employee-types', [EmployeeTypeController::class, 'store'])->name('employee-types.store');
    Route::get('employee-types', [EmployeeTypeController::class, 'index'])->name('employee-types.index');
    Route::get('employee-types/{employee_type}', [EmployeeTypeController::class, 'show'])->name('employee-types.show');
    Route::get('employee-types/{employee_type}/edit', [EmployeeTypeController::class, 'edit'])->name('employee-types.edit');
    Route::put('employee-types/{employee_type}', [EmployeeTypeController::class, 'update'])->name('employee-types.update');
    Route::patch('employee-types/{employee_type}', [EmployeeTypeController::class, 'update']);
    Route::put('employee-types/{employee_type}/toggle-status', [EmployeeTypeController::class, 'toggleStatus'])->name('employee-types.toggle-status');
    Route::get('employee-types/{employee_type}/delete', [EmployeeTypeController::class, 'confirmDelete'])->name('employee-types.confirm-delete');
    Route::delete('employee-types/{employee_type}', [EmployeeTypeController::class, 'destroy'])->name('employee-types.destroy');

    // Attendance - Scanner (special permission: scan_qr)
    Route::middleware('permission:attendances.scan_qr')->group(function () {
        Route::get('attendances/scanner', [AttendanceController::class, 'scanner'])->name('attendances.scanner');
        Route::post('attendances/scan', [AttendanceController::class, 'processScan'])->name('attendances.scan');
        Route::get('attendances/today-summary', [AttendanceController::class, 'todaySummary'])->name('attendances.today-summary');
    });

    // Attendance CRUD - CREATE routes first
    Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::patch('attendances/{attendance}', [AttendanceController::class, 'update']);
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    // QR Code Generation
    Route::get('employees/{employee}/qr-code', [AttendanceController::class, 'generateEmployeeQr'])->name('employees.qr-code');
    Route::get('departments/{department}/qr-code', [AttendanceController::class, 'generateDepartmentQr'])->name('departments.qr-code');
    Route::get('classrooms/{classroom}/qr-code', [AttendanceController::class, 'generateClassroomQr'])->name('classrooms.qr-code');
});
