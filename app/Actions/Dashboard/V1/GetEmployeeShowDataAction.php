<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use Modules\Employee\Http\Resources\Dashboard\V1\EmployeeResource;
use Modules\Employee\Models\Employee;

class GetEmployeeShowDataAction
{
    public function execute(Employee $employee): array
    {
        // Load relationships so they appear in the resource
        $employee->load(['school', 'department', 'employeeType']);

        return [
            'employee' => (new EmployeeResource($employee))->resolve(),
        ];
    }
}
