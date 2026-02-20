<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use Modules\Employee\Http\Resources\Dashboard\V1\EmployeeResource;
use Modules\Employee\Models\Employee;
use Modules\School\Http\Resources\Dashboard\V1\CourseResource;

class GetEmployeeShowDataAction
{
    public function execute(Employee $employee): array
    {
        $employee->load(['institution', 'department', 'courses']);
        $employee->loadCount('courses');

        $courses = $employee->courses()
            ->with(['department', 'program'])
            ->orderBy('name')
            ->get();

        return [
            'employee' => (new EmployeeResource($employee))->resolve(),
            'courses' => CourseResource::collection($courses)->resolve(),
            'stats' => [
                'courses_count' => $employee->courses_count,
                'is_on_probation' => $employee->isOnProbation(),
            ],
        ];
    }
}
