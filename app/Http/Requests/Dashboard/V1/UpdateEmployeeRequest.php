<?php

namespace Modules\Employee\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Employee\Models\Employee;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_code' => ['nullable', 'string', 'max:50', Rule::unique('employees', 'employee_code')->ignore($this->route('employee'))],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($this->route('employee'))],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'current_address' => ['nullable', 'string', 'max:500'],
            'institution_id' => ['nullable', 'integer', 'exists:institutions,id'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'position_id' => ['nullable', 'integer'],
            'job_title' => ['nullable', 'string', 'max:100'],
            'employee_type' => ['required', 'string', 'in:' . implode(',', array_keys(Employee::getEmployeeTypes()))],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'hire_date' => ['nullable', 'date'],
            'probation_date' => ['nullable', 'date'],
            'probation_end_date' => ['nullable', 'date', 'after:probation_date'],
            'certificate' => ['nullable', 'string', 'max:255'],
            'avatar_url' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'employee_code.unique' => 'This employee code is already in use.',
            'employee_type.required' => 'Employee type is required.',
            'employee_type.in' => 'Invalid employee type selected.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
            'probation_end_date.after' => 'Probation end date must be after probation start date.',
            'institution_id.exists' => 'The selected institution does not exist.',
            'department_id.exists' => 'The selected department does not exist.',
        ];
    }
}
