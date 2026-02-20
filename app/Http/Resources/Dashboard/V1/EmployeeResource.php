<?php

namespace Modules\Employee\Http\Resources\Dashboard\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Employee\Models\Employee;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'employee_code' => $this->employee_code,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'birth_place' => $this->birth_place,
            'current_address' => $this->current_address,
            'school_id' => $this->school_id,
            'department_id' => $this->department_id,
            'position_id' => $this->position_id,
            'type_employee_id' => $this->type_employee_id,
            'job_title' => $this->job_title,
            'employee_type' => $this->employee_type,
            'employee_type_label' => $this->getEmployeeTypeLabel(),
            'employee_type_name' => $this->whenLoaded('employeeType', fn() => $this->employeeType?->name),
            'salary' => $this->salary,
            'hire_date' => $this->hire_date?->format('Y-m-d'),
            'probation_date' => $this->probation_date?->format('Y-m-d'),
            'probation_end_date' => $this->probation_end_date?->format('Y-m-d'),
            'certificate' => $this->certificate,
            'certificate_image' => $this->certificate_image,
            'certificate_code' => $this->certificate_code,
            'avatar_url' => $this->avatar_url,
            'employee_qr_code' => $this->employee_qr_code,
            'employee_barcode' => $this->employee_barcode,
            'status' => $this->status,
            'is_on_probation' => $this->isOnProbation(),
            'school_name' => $this->whenLoaded('school', fn() => $this->school?->name),
            'department_name' => $this->whenLoaded('department', fn() => $this->department?->name),
            'courses_count' => $this->whenCounted('courses'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    protected function getEmployeeTypeLabel(): string
    {
        $types = Employee::getEmployeeTypes();
        return $types[$this->employee_type] ?? ucfirst($this->employee_type ?? '');
    }
}
