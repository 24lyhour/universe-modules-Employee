<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use Modules\Employee\Http\Resources\Dashboard\V1\EmployeeResource;
use Modules\Employee\Models\Employee;
use Modules\School\Models\Department;
use Modules\School\Models\Institution;

class GetEmployeeEditDataAction
{
    public function execute(Employee $employee): array
    {
        try {
            $institutions = Institution::where('status', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            $institutions = collect();
        }

        try {
            $departments = Department::where('status', true)
                ->when($employee->institution_id, function ($query) use ($employee) {
                    $query->where('institution_id', $employee->institution_id);
                })
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            $departments = collect();
        }

        // Transform employee types to array of {value, label} objects
        $employeeTypes = collect(Employee::getEmployeeTypes())
            ->map(fn($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();

        return [
            'employee' => (new EmployeeResource($employee))->resolve(),
            'institutions' => $institutions,
            'departments' => $departments,
            'employeeTypes' => $employeeTypes,
        ];
    }
}
