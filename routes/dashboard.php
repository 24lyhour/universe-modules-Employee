<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeController;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeTypeController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('employee.')->group(function () {
    // Departments AJAX endpoint (must be before resource to avoid {employee} capture)
    Route::get('employees/departments', [EmployeeController::class, 'getDepartments'])->name('employees.departments');

    // Employees
    Route::resource('employees', EmployeeController::class)->names('employees');
    Route::get('employees/{employee}/delete', [EmployeeController::class, 'confirmDelete'])->name('employees.confirm-delete');
    Route::put('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');

    // Employee Types
    Route::resource('employee-types', EmployeeTypeController::class)->names('employee-types');
    Route::get('employee-types/{employee_type}/delete', [EmployeeTypeController::class, 'confirmDelete'])->name('employee-types.confirm-delete');
    Route::put('employee-types/{employee_type}/toggle-status', [EmployeeTypeController::class, 'toggleStatus'])->name('employee-types.toggle-status');
});
