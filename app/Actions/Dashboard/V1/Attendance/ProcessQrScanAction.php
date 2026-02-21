<?php

namespace Modules\Employee\Actions\Dashboard\V1\Attendance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Employee\Models\Attendance;
use Modules\Employee\Models\Employee;
use Modules\School\Models\Department;
use Modules\School\Models\Classroom;

class ProcessQrScanAction
{
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $scanType = $data['scan_type']; // 'check_in' or 'check_out'
            $qrCode = $data['qr_code'];
            $locationType = $data['location_type'] ?? 'department'; // 'department' or 'classroom'
            $locationId = $data['location_id'] ?? null;

            // Find employee by QR code
            $employee = Employee::where('employee_qr_code', $qrCode)
                ->orWhere('uuid', $qrCode)
                ->orWhere('employee_code', $qrCode)
                ->first();

            if (!$employee) {
                return [
                    'success' => false,
                    'message' => 'Employee not found. Invalid QR code.',
                    'data' => null,
                ];
            }

            if (!$employee->status) {
                return [
                    'success' => false,
                    'message' => 'Employee is inactive.',
                    'data' => null,
                ];
            }

            $today = today();
            $now = now();

            // Find or create today's attendance
            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('attendance_date', $today)
                ->first();

            if ($scanType === 'check_in') {
                return $this->processCheckIn($employee, $attendance, $data, $today, $now, $locationType, $locationId);
            } else {
                return $this->processCheckOut($employee, $attendance, $data, $now, $locationType, $locationId);
            }
        });
    }

    private function processCheckIn(
        Employee $employee,
        ?Attendance $attendance,
        array $data,
        $today,
        $now,
        string $locationType,
        ?int $locationId
    ): array {
        if ($attendance && $attendance->check_in_time) {
            return [
                'success' => false,
                'message' => 'Already checked in today at ' . $attendance->check_in_time->format('H:i'),
                'data' => [
                    'employee' => $this->formatEmployeeData($employee),
                    'attendance' => $attendance,
                ],
            ];
        }

        // Determine status based on check-in time
        $status = $this->determineCheckInStatus($now);

        // Get location details
        $locationData = $this->getLocationData($locationType, $locationId, $employee);

        $attendanceData = [
            'employee_id' => $employee->id,
            'school_id' => $employee->school_id,
            'department_id' => $locationData['department_id'],
            'classroom_id' => $locationData['classroom_id'],
            'attendance_date' => $today,
            'check_in_time' => $now->format('H:i:s'),
            'status' => $status,
            'check_in_method' => Attendance::METHOD_QR_SCAN,
            'check_in_location' => $locationData['location_name'],
            'check_in_latitude' => $data['latitude'] ?? null,
            'check_in_longitude' => $data['longitude'] ?? null,
            'device_info' => $data['device_info'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
        ];

        if ($attendance) {
            $attendance->update($attendanceData);
        } else {
            $attendance = Attendance::create($attendanceData);
        }

        return [
            'success' => true,
            'message' => 'Check-in successful at ' . $now->format('H:i'),
            'data' => [
                'employee' => $this->formatEmployeeData($employee),
                'attendance' => $attendance->fresh(['employee', 'department', 'classroom']),
                'status' => $status,
            ],
        ];
    }

    private function processCheckOut(
        Employee $employee,
        ?Attendance $attendance,
        array $data,
        $now,
        string $locationType,
        ?int $locationId
    ): array {
        if (!$attendance || !$attendance->check_in_time) {
            return [
                'success' => false,
                'message' => 'No check-in record found for today. Please check in first.',
                'data' => [
                    'employee' => $this->formatEmployeeData($employee),
                ],
            ];
        }

        if ($attendance->check_out_time) {
            return [
                'success' => false,
                'message' => 'Already checked out today at ' . $attendance->check_out_time->format('H:i'),
                'data' => [
                    'employee' => $this->formatEmployeeData($employee),
                    'attendance' => $attendance,
                ],
            ];
        }

        // Get location details
        $locationData = $this->getLocationData($locationType, $locationId, $employee);

        // Update status if early leave
        $status = $this->determineCheckOutStatus($attendance->status, $now);

        $attendance->update([
            'check_out_time' => $now->format('H:i:s'),
            'status' => $status,
            'check_out_method' => Attendance::METHOD_QR_SCAN,
            'check_out_location' => $locationData['location_name'],
            'check_out_latitude' => $data['latitude'] ?? null,
            'check_out_longitude' => $data['longitude'] ?? null,
        ]);

        return [
            'success' => true,
            'message' => 'Check-out successful at ' . $now->format('H:i') . '. Work hours: ' . $attendance->fresh()->getFormattedWorkHours(),
            'data' => [
                'employee' => $this->formatEmployeeData($employee),
                'attendance' => $attendance->fresh(['employee', 'department', 'classroom']),
                'work_hours' => $attendance->fresh()->work_hours,
            ],
        ];
    }

    private function determineCheckInStatus($checkInTime): string
    {
        // Define work start time (configurable)
        $workStartTime = config('employee.work_start_time', '08:00');
        $lateThreshold = config('employee.late_threshold_minutes', 15);

        $workStart = \Carbon\Carbon::parse($workStartTime);
        $lateTime = $workStart->copy()->addMinutes($lateThreshold);

        if ($checkInTime->gt($lateTime)) {
            return Attendance::STATUS_LATE;
        }

        return Attendance::STATUS_PRESENT;
    }

    private function determineCheckOutStatus(string $currentStatus, $checkOutTime): string
    {
        // Define work end time (configurable)
        $workEndTime = config('employee.work_end_time', '17:00');
        $earlyLeaveThreshold = config('employee.early_leave_threshold_minutes', 30);

        $workEnd = \Carbon\Carbon::parse($workEndTime);
        $earlyLeaveTime = $workEnd->copy()->subMinutes($earlyLeaveThreshold);

        if ($checkOutTime->lt($earlyLeaveTime) && $currentStatus !== Attendance::STATUS_LATE) {
            return Attendance::STATUS_EARLY_LEAVE;
        }

        // If was late, keep that status
        return $currentStatus;
    }

    private function getLocationData(string $locationType, ?int $locationId, Employee $employee): array
    {
        $data = [
            'department_id' => $employee->department_id,
            'classroom_id' => null,
            'location_name' => null,
        ];

        if ($locationType === 'department' && $locationId) {
            $department = Department::find($locationId);
            if ($department) {
                $data['department_id'] = $department->id;
                $data['location_name'] = $department->name;
            }
        } elseif ($locationType === 'classroom' && $locationId) {
            $classroom = Classroom::with('department')->find($locationId);
            if ($classroom) {
                $data['classroom_id'] = $classroom->id;
                $data['department_id'] = $classroom->department_id;
                $data['location_name'] = $classroom->name . ' (' . ($classroom->department->name ?? 'N/A') . ')';
            }
        }

        // Fallback to employee's department
        if (!$data['location_name'] && $employee->department) {
            $data['location_name'] = $employee->department->name;
        }

        return $data;
    }

    private function formatEmployeeData(Employee $employee): array
    {
        return [
            'id' => $employee->id,
            'uuid' => $employee->uuid,
            'employee_code' => $employee->employee_code,
            'full_name' => $employee->full_name,
            'avatar_url' => $employee->avatar_url,
            'department' => $employee->department?->name,
            'job_title' => $employee->job_title,
        ];
    }
}
