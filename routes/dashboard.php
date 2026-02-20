<?php

use Illuminate\Support\Facades\Route;
use Modules\Employee\Http\Controllers\Dashboard\V1\EmployeeController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('employee.')->group(function () {
    // Departments AJAX endpoint (must be before resource to avoid {employee} capture)
    Route::get('employees/departments', [EmployeeController::class, 'getDepartments'])->name('employees.departments');

    // Employees
    Route::resource('employees', EmployeeController::class)->names('employees');
    Route::get('employees/{employee}/delete', [EmployeeController::class, 'confirmDelete'])->name('employees.confirm-delete');
    Route::put('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
});
