<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use Modules\Employee\Models\Employee;
use Modules\School\Models\Department;
use Modules\School\Models\Institution;

class GetEmployeeCreateDataAction
{
    public function execute(?int $institutionId = null): array
    {
        $institutions = Institution::where('status', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $departments = collect();
        if ($institutionId) {
            $departments = Department::where('institution_id', $institutionId)
                ->where('status', true)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return [
            'institutions' => $institutions,
            'departments' => $departments,
            'employeeTypes' => Employee::getEmployeeTypes(),
        ];
    }
}
