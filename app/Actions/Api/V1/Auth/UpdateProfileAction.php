<?php

namespace Modules\Employee\Actions\Api\V1\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Http\Resources\Api\V1\EmployeeResource;
use Modules\Employee\Models\Employee;

class UpdateProfileAction
{
    /**
     * Update employee profile.
     */
    public function execute(User $user, array $data): array
    {
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return [
                'success' => false,
                'message' => 'Employee not found',
            ];
        }

        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar']) {
            // Delete old avatar if exists
            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }

            // Store new avatar
            $path = $data['avatar']->store('employees/avatars', 'public');
            $data['avatar'] = $path;
        } else {
            unset($data['avatar']);
        }

        // Update employee
        $employee->update([
            'first_name' => $data['first_name'] ?? $employee->first_name,
            'last_name' => $data['last_name'] ?? $employee->last_name,
            'phone_number' => $data['phone_number'] ?? $employee->phone_number,
            'gender' => $data['gender'] ?? $employee->gender,
            'date_of_birth' => $data['date_of_birth'] ?? $employee->date_of_birth,
            'birth_place' => $data['birth_place'] ?? $employee->birth_place,
            'current_address' => $data['current_address'] ?? $employee->current_address,
            'avatar' => $data['avatar'] ?? $employee->avatar,
        ]);

        // Refresh the model
        $employee->refresh();
        $employee->load(['school', 'department', 'employeeType', 'user']);

        return [
            'success' => true,
            'message' => 'Profile updated successfully',
            'employee' => new EmployeeResource($employee),
        ];
    }
}
