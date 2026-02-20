import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';

// Zod schema for Employee form validation
export const employeeSchema = z.object({
    employee_code: z
        .string({ required_error: 'Employee code is required' })
        .min(1, 'Employee code is required')
        .max(50, 'Employee code must be less than 50 characters'),
    first_name: z
        .string({ required_error: 'First name is required' })
        .min(1, 'First name is required')
        .max(100, 'First name must be less than 100 characters'),
    last_name: z
        .string({ required_error: 'Last name is required' })
        .min(1, 'Last name is required')
        .max(100, 'Last name must be less than 100 characters'),
    email: z
        .string()
        .email('Invalid email address')
        .optional()
        .nullable()
        .or(z.literal('')),
    phone_number: z
        .string()
        .max(20, 'Phone number must be less than 20 characters')
        .optional()
        .nullable(),
    gender: z.enum(['male', 'female', 'other']).optional().nullable(),
    date_of_birth: z.string().optional().nullable(),
    birth_place: z
        .string()
        .max(255, 'Birth place must be less than 255 characters')
        .optional()
        .nullable(),
    current_address: z
        .string()
        .max(500, 'Address must be less than 500 characters')
        .optional()
        .nullable(),
    institution_id: z.number().int().positive().optional().nullable(),
    department_id: z.number().int().positive().optional().nullable(),
    position_id: z.number().int().positive().optional().nullable(),
    job_title: z
        .string()
        .max(100, 'Job title must be less than 100 characters')
        .optional()
        .nullable(),
    employee_type: z.enum(['full_time', 'part_time', 'contract', 'intern']).optional().nullable(),
    salary: z.number().min(0, 'Salary must be positive').optional().nullable(),
    hire_date: z.string().optional().nullable(),
    probation_date: z.string().optional().nullable(),
    probation_end_date: z.string().optional().nullable(),
    certificate: z
        .string()
        .max(255, 'Certificate must be less than 255 characters')
        .optional()
        .nullable(),
    certificate_image: z.string().optional().nullable(),
    certificate_code: z
        .string()
        .max(100, 'Certificate code must be less than 100 characters')
        .optional()
        .nullable(),
    avatar_url: z.string().optional().nullable(),
    status: z.boolean().default(true),
});

// TypedSchema for vee-validate
export const employeeValidationSchema = toTypedSchema(employeeSchema);

// Type inference from Zod schema
export type EmployeeFormValues = z.infer<typeof employeeSchema>;
