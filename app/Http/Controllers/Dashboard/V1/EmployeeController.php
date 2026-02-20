<?php

namespace Modules\Employee\Http\Controllers\Dashboard\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Momentum\Modal\Modal;
use Modules\Employee\Actions\Dashboard\V1\CreateEmployeeAction;
use Modules\Employee\Actions\Dashboard\V1\DeleteEmployeeAction;
use Modules\Employee\Actions\Dashboard\V1\GetEmployeeCreateDataAction;
use Modules\Employee\Actions\Dashboard\V1\GetEmployeeEditDataAction;
use Modules\Employee\Actions\Dashboard\V1\GetEmployeeIndexDataAction;
use Modules\Employee\Actions\Dashboard\V1\GetEmployeeShowDataAction;
use Modules\Employee\Actions\Dashboard\V1\ToggleEmployeeStatusAction;
use Modules\Employee\Actions\Dashboard\V1\UpdateEmployeeAction;
use Modules\Employee\Http\Requests\Dashboard\V1\StoreEmployeeRequest;
use Modules\Employee\Http\Requests\Dashboard\V1\UpdateEmployeeRequest;
use Modules\Employee\Http\Resources\Dashboard\V1\EmployeeResource;
use Modules\Employee\Models\Employee;
use Modules\Employee\Services\EmployeeService;
use Modules\School\Models\Department;
use Modules\School\Models\Institution;

class EmployeeController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService,
        protected GetEmployeeIndexDataAction $getIndexDataAction,
        protected GetEmployeeShowDataAction $getShowDataAction,
        protected GetEmployeeCreateDataAction $getCreateDataAction,
        protected GetEmployeeEditDataAction $getEditDataAction,
        protected CreateEmployeeAction $createAction,
        protected UpdateEmployeeAction $updateAction,
        protected DeleteEmployeeAction $deleteAction,
        protected ToggleEmployeeStatusAction $toggleStatusAction,
    ) {}

    /**
     * Display a listing of employees.
     */
    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);
        $filters = $request->only(['search', 'status', 'employee_type', 'institution_id', 'department_id']);

        $data = $this->getIndexDataAction->execute($perPage, $filters);

        // Add institutions for filters (handle case where table doesn't exist)
        try {
            $data['institutions'] = Institution::where('status', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            $data['institutions'] = collect([]);
        }

        return Inertia::render('employee::Dashboard/V1/Employee/Index', $data);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(Request $request): Modal
    {
        $institutionId = $request->input('institution_id') ? (int) $request->input('institution_id') : null;
        $data = $this->getCreateDataAction->execute($institutionId);

        // Add generated employee code
        $data['generatedCode'] = $this->employeeService->generateEmployeeCode();

        return Inertia::modal('employee::Dashboard/V1/Employee/Create', $data)
            ->baseRoute('employee.employees.index');
    }

    /**
     * Store a newly created employee.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $this->createAction->execute($request->validated());

        return redirect()
            ->route('employee.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): Response
    {
        $data = $this->getShowDataAction->execute($employee);

        return Inertia::render('employee::Dashboard/V1/Employee/Show', $data);
    }

    /**
     * Show the form for editing the employee.
     */
    public function edit(Employee $employee): Modal
    {
        $data = $this->getEditDataAction->execute($employee);

        return Inertia::modal('employee::Dashboard/V1/Employee/Edit', $data)
            ->baseRoute('employee.employees.index');
    }

    /**
     * Update the specified employee.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->updateAction->execute($employee, $request->validated());

        return redirect()
            ->route('employee.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Show delete confirmation modal.
     */
    public function confirmDelete(Employee $employee): Modal
    {
        $employee->load(['institution', 'department']);
        $employee->loadCount('courses');

        return Inertia::modal('employee::Dashboard/V1/Employee/Delete', [
            'employee' => (new EmployeeResource($employee))->resolve(),
        ])->baseRoute('employee.employees.index');
    }

    /**
     * Remove the specified employee.
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        $this->deleteAction->execute($employee);

        return redirect()
            ->route('employee.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    /**
     * Toggle employee status.
     */
    public function toggleStatus(Employee $employee): RedirectResponse
    {
        $this->toggleStatusAction->execute($employee);

        $status = $employee->status ? 'activated' : 'deactivated';

        return redirect()
            ->back()
            ->with('success', "Employee {$status} successfully.");
    }

    /**
     * Get departments for a specific institution (AJAX).
     */
    public function getDepartments(Request $request): \Illuminate\Http\JsonResponse
    {
        $institutionId = $request->input('institution_id');

        try {
            $departments = Department::where('institution_id', $institutionId)
                ->where('status', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            $departments = collect([]);
        }

        return response()->json($departments);
    }
}
