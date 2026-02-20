<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use Modules\Employee\Http\Resources\Dashboard\V1\EmployeeResource;
use Modules\Employee\Models\Employee;

class GetEmployeeIndexDataAction
{
    public function execute(int $perPage = 10, array $filters = []): array
    {
        $query = Employee::with(['institution', 'department'])
            ->withCount('courses');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['institution_id'])) {
            $query->where('institution_id', $filters['institution_id']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['employee_type']) && $filters['employee_type'] !== 'all') {
            $query->where('employee_type', $filters['employee_type']);
        }

        if (isset($filters['status']) && $filters['status'] !== '' && $filters['status'] !== 'all') {
            $query->where('status', $filters['status'] === '1' || $filters['status'] === 'active');
        }

        $employees = $query->latest()->paginate($perPage);

        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', true)->count(),
            'inactive' => Employee::where('status', false)->count(),
            'full_time' => Employee::where('employee_type', 'full_time')->count(),
            'part_time' => Employee::where('employee_type', 'part_time')->count(),
            'contract' => Employee::where('employee_type', 'contract')->count(),
        ];

        return [
            'employees' => [
                'data' => EmployeeResource::collection($employees)->resolve(),
                'meta' => [
                    'current_page' => $employees->currentPage(),
                    'last_page' => $employees->lastPage(),
                    'per_page' => $employees->perPage(),
                    'total' => $employees->total(),
                ],
            ],
            'filters' => $filters,
            'stats' => $stats,
            'employeeTypes' => Employee::getEmployeeTypes(),
        ];
    }
}
