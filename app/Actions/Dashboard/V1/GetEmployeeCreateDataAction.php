<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use Modules\Employee\Models\Employee;
use Modules\School\Models\Department;
use Modules\School\Models\Institution;

class GetEmployeeCreateDataAction
{
    public function execute(?int $institutionId = null): array
    {
        try {
            $institutions = Institution::where('status', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            $institutions = collect();
        }

        $departments = collect();
        if ($institutionId) {
            try {
                $departments = Department::where('institution_id', $institutionId)
                    ->where('status', true)
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();
            } catch (\Exception $e) {
                $departments = collect();
            }
        }

        // Transform employee types to array of {value, label} objects
        $employeeTypes = collect(Employee::getEmployeeTypes())
            ->map(fn($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();

        return [
            'institutions' => $institutions,
            'departments' => $departments,
            'employeeTypes' => $employeeTypes,
        ];
    }
}
