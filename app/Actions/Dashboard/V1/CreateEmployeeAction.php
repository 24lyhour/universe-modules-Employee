<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;

class CreateEmployeeAction
{
    public function execute(array $data): Employee
    {
        $data['uuid'] = (string) Str::uuid();
        $data['created_by'] = Auth::id();

        return Employee::create($data);
    }
}
