<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use Illuminate\Support\Facades\Auth;
use Modules\Employee\Models\Employee;

class UpdateEmployeeAction
{
    public function execute(Employee $employee, array $data): Employee
    {
        $data['updated_by'] = Auth::id();
        $employee->update($data);

        return $employee->fresh();
    }
}
