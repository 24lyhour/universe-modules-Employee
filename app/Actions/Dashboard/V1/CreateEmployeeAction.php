<?php

namespace Modules\Employee\Actions\Dashboard\V1;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;

class CreateEmployeeAction
{
    public function execute(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            // Extract account creation fields
            $createAccount = $data['create_account'] ?? false;
            $password = $data['password'] ?? null;

            // Remove non-employee fields
            unset($data['create_account'], $data['password'], $data['password_confirmation']);

            // Create User account if requested
            $userId = null;
            if ($createAccount && !empty($data['email']) && $password) {
                $user = User::create([
                    'name' => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                    'email' => $data['email'],
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]);

                // Assign employee role if exists
                if (method_exists($user, 'assignRole')) {
                    try {
                        $user->assignRole('employee');
                    } catch (\Exception $e) {
                        // Role might not exist, continue without it
                    }
                }

                $userId = $user->id;
            }

            // Prepare employee data
            $data['uuid'] = (string) Str::uuid();
            $data['created_by'] = Auth::id();
            $data['user_id'] = $userId;

            return Employee::create($data);
        });
    }
}
