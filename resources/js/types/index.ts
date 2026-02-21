// Employee Module Types

export type EmployeeType = 'full_time' | 'part_time' | 'contract' | 'intern';
export type Gender = 'male' | 'female' | 'other';

export interface Employee {
    id: number;
    uuid: string;
    employee_code: string;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string | null;
    phone_number: string | null;
    gender: Gender | null;
    date_of_birth: string | null;
    birth_place: string | null;
    current_address: string | null;
    school_id: number | null;
    department_id: number | null;
    position_id: number | null;
    type_employee_id: number | null;
    job_title: string | null;
    employee_type: EmployeeType | null;
    employee_type_label: string | null;
    employee_type_name: string | null;
    salary: number | null;
    hire_date: string | null;
    probation_date: string | null;
    probation_end_date: string | null;
    certificate: string | null;
    certificate_image: string | null;
    certificate_code: string | null;
    avatar_url: string | null;
    employee_qr_code: string | null;
    employee_barcode: string | null;
    status: boolean;
    is_on_probation: boolean;
    school_name: string | null;
    department_name: string | null;
    courses_count: number | null;
    created_at: string;
    updated_at: string;
}

export interface EmployeeStats {
    total: number;
    active: number;
    inactive: number;
}

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
}

export interface EmployeeFilters {
    status?: string;
    search?: string;
    employee_type?: string;
    school_id?: string;
    department_id?: string;
}

export interface EmployeeFormData {
    employee_code: string;
    first_name: string;
    last_name: string;
    email: string;
    phone_number: string;
    gender: Gender | null;
    date_of_birth: string;
    birth_place: string;
    current_address: string;
    school_id: number | null;
    department_id: number | null;
    position_id: number | null;
    type_employee_id: number | null;
    job_title: string;
    employee_type: EmployeeType | null;
    salary: number | null;
    hire_date: string;
    probation_date: string;
    probation_end_date: string;
    certificate: string;
    certificate_image: string;
    certificate_code: string;
    avatar_url: string;
    status: boolean;
}

export interface SchoolOption {
    id: number;
    name: string;
}

export interface DepartmentOption {
    id: number;
    name: string;
}

export interface EmployeeTypeOption {
    value: EmployeeType;
    label: string;
}

export interface EmployeeIndexProps {
    employees: PaginatedResponse<Employee>;
    filters: EmployeeFilters;
    stats: EmployeeStats;
    schools: SchoolOption[];
}

export interface EmployeeShowProps {
    employee: Employee;
}

export interface EmployeeCreateProps {
    schools: SchoolOption[];
    departments: DepartmentOption[];
    employeeTypes: EmployeeTypeOption[];
    generatedCode: string;
}

export interface EmployeeEditProps {
    employee: Employee;
    schools: SchoolOption[];
    departments: DepartmentOption[];
    employeeTypes: EmployeeTypeOption[];
}

export interface EmployeeDeleteProps {
    employee: Employee;
}

// Employee Type (Model) Types
export interface EmployeeTypeModel {
    id: number;
    uuid: string;
    name: string;
    description: string | null;
    time_start: string | null;
    time_end: string | null;
    status: boolean;
    employees_count: number | null;
    created_at: string;
    updated_at: string;
}

export interface EmployeeTypeStats {
    total: number;
    active: number;
    inactive: number;
}

export interface EmployeeTypeFilters {
    status?: string;
    search?: string;
}

export interface EmployeeTypeFormData {
    name: string;
    description: string;
    time_start: string;
    time_end: string;
    status: boolean;
}

export interface EmployeeTypeIndexProps {
    employeeTypes: PaginatedResponse<EmployeeTypeModel>;
    filters: EmployeeTypeFilters;
    stats: EmployeeTypeStats;
}

export interface EmployeeTypeShowProps {
    employeeType: EmployeeTypeModel;
}

export interface EmployeeTypeCreateProps {}

export interface EmployeeTypeEditProps {
    employeeType: EmployeeTypeModel;
}

export interface EmployeeTypeDeleteProps {
    employeeType: EmployeeTypeModel;
}

// Attendance Types
export type AttendanceStatus = 'present' | 'absent' | 'late' | 'early_leave' | 'half_day' | 'on_leave';
export type CheckInMethod = 'qr_scan' | 'manual' | 'biometric' | 'face_recognition';

export interface Attendance {
    id: number;
    uuid: string;
    employee_id: number;
    employee_name: string | null;
    employee_code: string | null;
    employee_avatar: string | null;
    school_id: number | null;
    school_name: string | null;
    department_id: number | null;
    department_name: string | null;
    classroom_id: number | null;
    classroom_name: string | null;
    attendance_date: string;
    attendance_date_formatted: string;
    check_in_time: string | null;
    check_out_time: string | null;
    status: AttendanceStatus;
    status_label: string;
    check_in_method: CheckInMethod;
    check_in_method_label: string;
    check_out_method: CheckInMethod | null;
    check_out_method_label: string | null;
    check_in_location: string | null;
    check_out_location: string | null;
    check_in_coordinates: { lat: number; lng: number } | null;
    check_out_coordinates: { lat: number; lng: number } | null;
    work_hours: number | null;
    work_hours_formatted: string;
    overtime_hours: number | null;
    notes: string | null;
    device_info: string | null;
    ip_address: string | null;
    created_at: string;
    updated_at: string;
}

export interface AttendanceStats {
    total_employees: number;
    present_today: number;
    absent_today: number;
    late_today: number;
    on_leave_today: number;
}

export interface AttendanceFilters {
    search?: string;
    status?: string;
    employee_id?: number;
    department_id?: number;
    date_from?: string;
    date_to?: string;
}

export interface AttendanceFormData {
    employee_id: number | null;
    attendance_date: string;
    check_in_time: string;
    check_out_time: string;
    status: AttendanceStatus;
    department_id: number | null;
    classroom_id: number | null;
    notes: string;
}

export interface SelectOption {
    value: number;
    label: string;
    department?: string;
    department_id?: number;
}

export interface AttendanceIndexProps {
    attendances: PaginatedResponse<Attendance>;
    filters: AttendanceFilters;
    stats: AttendanceStats;
    statuses: Record<AttendanceStatus, string>;
    employeeOptions: { id: number; name: string }[];
    departmentOptions: DepartmentOption[];
}

export interface AttendanceShowProps {
    attendance: Attendance;
    statuses: Record<AttendanceStatus, string>;
    methods: Record<CheckInMethod, string>;
}

export interface AttendanceCreateProps {
    employeeOptions: SelectOption[];
    departmentOptions: SelectOption[];
    statuses: Record<AttendanceStatus, string>;
}

export interface AttendanceEditProps {
    attendance: Attendance;
    statuses: Record<AttendanceStatus, string>;
}

export interface AttendanceScannerProps {
    departmentOptions: SelectOption[];
    classroomOptions: SelectOption[];
}
