<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import EmployeeForm from '@employee/Components/Dashboard/EmployeeForm.vue';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { employeeSchema } from '@employee/validation/employeeSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { EmployeeFormData, EmployeeEditProps, DepartmentOption } from '@employee/types';

const props = defineProps<EmployeeEditProps>();

const { show, close, redirect } = useModal();

const isOpen = computed({
    get: () => show.value,
    set: (val: boolean) => {
        if (!val) {
            close();
            redirect();
        }
    },
});

const departments = ref<DepartmentOption[]>(props.departments || []);

const form = useForm<EmployeeFormData>({
    employee_code: props.employee.employee_code,
    first_name: props.employee.first_name,
    last_name: props.employee.last_name,
    email: props.employee.email || '',
    phone_number: props.employee.phone_number || '',
    gender: props.employee.gender,
    date_of_birth: props.employee.date_of_birth || '',
    birth_place: props.employee.birth_place || '',
    current_address: props.employee.current_address || '',
    institution_id: props.employee.institution_id,
    department_id: props.employee.department_id,
    position_id: props.employee.position_id,
    job_title: props.employee.job_title || '',
    employee_type: props.employee.employee_type,
    salary: props.employee.salary,
    hire_date: props.employee.hire_date || '',
    probation_date: props.employee.probation_date || '',
    probation_end_date: props.employee.probation_end_date || '',
    certificate: props.employee.certificate || '',
    certificate_image: props.employee.certificate_image || '',
    certificate_code: props.employee.certificate_code || '',
    avatar_url: props.employee.avatar_url || '',
    status: props.employee.status,
});

// Use shared validation composable
const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    employeeSchema,
    ['employee_code', 'first_name', 'last_name'] // Required fields
);

// Get form data for validation
const getFormData = () => ({
    employee_code: form.employee_code,
    first_name: form.first_name,
    last_name: form.last_name,
    email: form.email || null,
    phone_number: form.phone_number || null,
    gender: form.gender,
    date_of_birth: form.date_of_birth || null,
    birth_place: form.birth_place || null,
    current_address: form.current_address || null,
    institution_id: form.institution_id,
    department_id: form.department_id,
    position_id: form.position_id,
    job_title: form.job_title || null,
    employee_type: form.employee_type,
    salary: form.salary,
    hire_date: form.hire_date || null,
    probation_date: form.probation_date || null,
    probation_end_date: form.probation_end_date || null,
    certificate: form.certificate || null,
    certificate_image: form.certificate_image || null,
    certificate_code: form.certificate_code || null,
    avatar_url: form.avatar_url || null,
    status: form.status,
});

// Watch form changes to validate in real-time
watch([() => form.first_name, () => form.last_name], () => validateForm(getFormData()));

// Check if form is valid for submit button state
const isFormInvalid = createIsFormInvalid(getFormData);

// Handle institution change to fetch departments
const handleInstitutionChange = async (institutionId: number | null) => {
    if (!institutionId) {
        departments.value = [];
        return;
    }

    try {
        const response = await fetch(`/dashboard/employees/departments?institution_id=${institutionId}`);
        if (response.ok) {
            departments.value = await response.json();
        }
    } catch (error) {
        console.error('Failed to fetch departments:', error);
        departments.value = [];
    }
};

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/employees/${props.employee.id}`, {
            onSuccess: () => {
                toast.success('Employee updated successfully.');
                setTimeout(() => {
                    close();
                    redirect();
                }, 100);
            },
        });
    });
};

const handleCancel = () => {
    close();
    redirect();
};
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Edit Employee"
        description="Update employee information"
        mode="edit"
        size="2xl"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <EmployeeForm
            v-model="form"
            mode="edit"
            :institutions="props.institutions"
            :departments="departments"
            :employee-types="props.employeeTypes"
            @institution-change="handleInstitutionChange"
        />
    </ModalForm>
</template>
