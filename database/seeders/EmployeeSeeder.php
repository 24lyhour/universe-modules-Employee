<?php

namespace Modules\Employee\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            [
                'first_name' => 'John',
                'last_name' => 'Smith',
                'email' => 'john.smith@example.com',
                'phone_number' => '+1 234 567 8901',
                'gender' => 'male',
                'date_of_birth' => '1990-05-15',
                'birth_place' => 'New York, USA',
                'current_address' => '123 Main Street, New York, NY 10001',
                'job_title' => 'Software Engineer',
                'employee_type' => 'full_time',
                'salary' => 75000.00,
                'hire_date' => '2022-01-15',
                'status' => true,
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'sarah.johnson@example.com',
                'phone_number' => '+1 234 567 8902',
                'gender' => 'female',
                'date_of_birth' => '1988-08-22',
                'birth_place' => 'Los Angeles, USA',
                'current_address' => '456 Oak Avenue, Los Angeles, CA 90001',
                'job_title' => 'Project Manager',
                'employee_type' => 'full_time',
                'salary' => 85000.00,
                'hire_date' => '2021-06-01',
                'status' => true,
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'email' => 'michael.brown@example.com',
                'phone_number' => '+1 234 567 8903',
                'gender' => 'male',
                'date_of_birth' => '1995-03-10',
                'birth_place' => 'Chicago, USA',
                'current_address' => '789 Pine Road, Chicago, IL 60601',
                'job_title' => 'UI/UX Designer',
                'employee_type' => 'full_time',
                'salary' => 65000.00,
                'hire_date' => '2023-02-20',
                'probation_date' => '2023-02-20',
                'probation_end_date' => '2023-08-20',
                'status' => true,
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Davis',
                'email' => 'emily.davis@example.com',
                'phone_number' => '+1 234 567 8904',
                'gender' => 'female',
                'date_of_birth' => '1992-11-28',
                'birth_place' => 'Houston, USA',
                'current_address' => '321 Elm Street, Houston, TX 77001',
                'job_title' => 'Marketing Specialist',
                'employee_type' => 'part_time',
                'salary' => 35000.00,
                'hire_date' => '2023-05-10',
                'status' => true,
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.wilson@example.com',
                'phone_number' => '+1 234 567 8905',
                'gender' => 'male',
                'date_of_birth' => '1985-07-04',
                'birth_place' => 'Phoenix, USA',
                'current_address' => '654 Cedar Lane, Phoenix, AZ 85001',
                'job_title' => 'Senior Developer',
                'employee_type' => 'full_time',
                'salary' => 95000.00,
                'hire_date' => '2019-09-15',
                'certificate' => 'AWS Solutions Architect',
                'certificate_code' => 'AWS-SA-2023-001',
                'status' => true,
            ],
            [
                'first_name' => 'Jessica',
                'last_name' => 'Martinez',
                'email' => 'jessica.martinez@example.com',
                'phone_number' => '+1 234 567 8906',
                'gender' => 'female',
                'date_of_birth' => '1993-02-14',
                'birth_place' => 'San Diego, USA',
                'current_address' => '987 Maple Drive, San Diego, CA 92101',
                'job_title' => 'HR Manager',
                'employee_type' => 'full_time',
                'salary' => 70000.00,
                'hire_date' => '2020-03-01',
                'certificate' => 'SHRM Certified Professional',
                'certificate_code' => 'SHRM-CP-2022-456',
                'status' => true,
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Anderson',
                'email' => 'james.anderson@example.com',
                'phone_number' => '+1 234 567 8907',
                'gender' => 'male',
                'date_of_birth' => '1991-09-30',
                'birth_place' => 'Dallas, USA',
                'current_address' => '147 Birch Court, Dallas, TX 75201',
                'job_title' => 'DevOps Engineer',
                'employee_type' => 'contract',
                'salary' => 80000.00,
                'hire_date' => '2023-07-01',
                'status' => true,
            ],
            [
                'first_name' => 'Ashley',
                'last_name' => 'Taylor',
                'email' => 'ashley.taylor@example.com',
                'phone_number' => '+1 234 567 8908',
                'gender' => 'female',
                'date_of_birth' => '1997-12-05',
                'birth_place' => 'Seattle, USA',
                'current_address' => '258 Walnut Street, Seattle, WA 98101',
                'job_title' => 'Junior Developer',
                'employee_type' => 'intern',
                'salary' => 40000.00,
                'hire_date' => '2024-01-15',
                'probation_date' => '2024-01-15',
                'probation_end_date' => '2024-07-15',
                'status' => true,
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Thomas',
                'email' => 'robert.thomas@example.com',
                'phone_number' => '+1 234 567 8909',
                'gender' => 'male',
                'date_of_birth' => '1987-04-18',
                'birth_place' => 'Boston, USA',
                'current_address' => '369 Spruce Avenue, Boston, MA 02101',
                'job_title' => 'Data Analyst',
                'employee_type' => 'full_time',
                'salary' => 72000.00,
                'hire_date' => '2021-11-01',
                'status' => false,
            ],
            [
                'first_name' => 'Amanda',
                'last_name' => 'Garcia',
                'email' => 'amanda.garcia@example.com',
                'phone_number' => '+1 234 567 8910',
                'gender' => 'female',
                'date_of_birth' => '1994-06-25',
                'birth_place' => 'Miami, USA',
                'current_address' => '741 Palm Boulevard, Miami, FL 33101',
                'job_title' => 'Account Manager',
                'employee_type' => 'full_time',
                'salary' => 68000.00,
                'hire_date' => '2022-08-15',
                'certificate' => 'PMP Certification',
                'certificate_code' => 'PMP-2023-789',
                'status' => true,
            ],
        ];

        $employeeCount = Employee::count();

        foreach ($employees as $index => $data) {
            $code = sprintf('EMP-%06d', $employeeCount + $index + 1);

            // Generate avatar URL using UI Avatars service
            $initials = strtoupper(substr($data['first_name'], 0, 1) . substr($data['last_name'], 0, 1));
            $colors = ['4f46e5', '7c3aed', 'db2777', 'ea580c', '16a34a', '0891b2', '6366f1', 'a855f7'];
            $bgColor = $colors[$index % count($colors)];
            $avatarUrl = "https://ui-avatars.com/api/?name={$initials}&background={$bgColor}&color=ffffff&size=200&font-size=0.4&bold=true";

            // Generate certificate image for employees with certificates
            $certificateImage = null;
            if (!empty($data['certificate'])) {
                $certificateImage = "https://placehold.co/800x600/1e40af/ffffff?text=" . urlencode($data['certificate']);
            }

            Employee::create([
                'uuid' => (string) Str::uuid(),
                'employee_code' => $code,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'] ?? null,
                'gender' => $data['gender'] ?? null,
                'date_of_birth' => $data['date_of_birth'] ?? null,
                'birth_place' => $data['birth_place'] ?? null,
                'current_address' => $data['current_address'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'employee_type' => $data['employee_type'] ?? null,
                'salary' => $data['salary'] ?? null,
                'hire_date' => $data['hire_date'] ?? null,
                'probation_date' => $data['probation_date'] ?? null,
                'probation_end_date' => $data['probation_end_date'] ?? null,
                'certificate' => $data['certificate'] ?? null,
                'certificate_code' => $data['certificate_code'] ?? null,
                'certificate_image' => $certificateImage,
                'avatar_url' => $avatarUrl,
                'status' => $data['status'],
            ]);
        }

        $this->command->info('Created ' . count($employees) . ' employees with images.');
    }
}
