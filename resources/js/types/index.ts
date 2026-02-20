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
    institution_id: number | null;
    department_id: number | null;
    position_id: number | null;
    job_title: string | null;
    employee_type: EmployeeType | null;
    employee_type_label: string | null;
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
    institution_name: string | null;
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
    institution_id?: string;
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
    institution_id: number | null;
    department_id: number | null;
    position_id: number | null;
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

export interface InstitutionOption {
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
    institutions: InstitutionOption[];
}

export interface EmployeeShowProps {
    employee: Employee;
}

export interface EmployeeCreateProps {
    institutions: InstitutionOption[];
    departments: DepartmentOption[];
    employeeTypes: EmployeeTypeOption[];
    generatedCode: string;
}

export interface EmployeeEditProps {
    employee: Employee;
    institutions: InstitutionOption[];
    departments: DepartmentOption[];
    employeeTypes: EmployeeTypeOption[];
}

export interface EmployeeDeleteProps {
    employee: Employee;
}
