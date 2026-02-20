<script setup lang="ts">
import { computed, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Separator } from '@/components/ui/separator';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { ImageUpload } from '@/components/shared';
import type { InertiaForm } from '@inertiajs/vue3';
import type { EmployeeFormData, InstitutionOption, DepartmentOption, EmployeeTypeOption } from '../../types';

interface Props {
    mode?: 'create' | 'edit';
    institutions?: InstitutionOption[];
    departments?: DepartmentOption[];
    employeeTypes?: EmployeeTypeOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    institutions: () => [],
    departments: () => [],
    employeeTypes: () => [],
});

const emit = defineEmits<{
    institutionChange: [institutionId: number | null];
}>();

const model = defineModel<InertiaForm<EmployeeFormData>>({ required: true });

// Computed for institution select
const selectedInstitution = computed({
    get: () => model.value.institution_id?.toString(),
    set: (value: string | undefined) => {
        const newValue = value ? parseInt(value) : null;
        model.value.institution_id = newValue;
        model.value.department_id = null; // Reset department when institution changes
        emit('institutionChange', newValue);
    },
});

// Computed for department select
const selectedDepartment = computed({
    get: () => model.value.department_id?.toString(),
    set: (value: string | undefined) => {
        model.value.department_id = value ? parseInt(value) : null;
    },
});

// Computed for gender select
const selectedGender = computed({
    get: () => model.value.gender || undefined,
    set: (value: string | undefined) => {
        model.value.gender = (value as 'male' | 'female' | 'other') || null;
    },
});

// Computed for employee type select
const selectedEmployeeType = computed({
    get: () => model.value.employee_type || undefined,
    set: (value: string | undefined) => {
        model.value.employee_type = (value as 'full_time' | 'part_time' | 'contract' | 'intern') || null;
    },
});

// Convert avatar_url string to array for ImageUpload component
const avatarImages = computed({
    get: () => model.value.avatar_url ? [model.value.avatar_url] : [],
    set: (value: string[]) => {
        model.value.avatar_url = value.length > 0 ? value[0] : '';
    },
});

// Convert certificate_image string to array for ImageUpload component
const certificateImages = computed({
    get: () => model.value.certificate_image ? [model.value.certificate_image] : [],
    set: (value: string[]) => {
        model.value.certificate_image = value.length > 0 ? value[0] : '';
    },
});

// Computed for salary (convert null to undefined for v-model.number)
const salaryValue = computed({
    get: () => model.value.salary ?? undefined,
    set: (value: number | undefined) => {
        model.value.salary = value ?? null;
    },
});

// Status switch
const isActive = computed({
    get: () => model.value.status,
    set: (value: boolean) => {
        model.value.status = value;
    },
});

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' },
];
</script>

<template>
    <div class="space-y-6">
        <!-- Basic Information Section -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Basic Information</h3>
                <p class="text-sm text-muted-foreground">
                    {{ mode === 'create' ? 'Enter the employee details' : 'Update the employee details' }}
                </p>
            </div>
            <Separator />

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="employee_code">Employee Code <span class="text-destructive">*</span></Label>
                    <Input
                        id="employee_code"
                        v-model="model.employee_code"
                        type="text"
                        placeholder="Enter employee code"
                        :disabled="mode === 'edit'"
                    />
                    <p v-if="model.errors.employee_code" class="text-sm text-destructive">
                        {{ model.errors.employee_code }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="status">Status</Label>
                    <div class="flex items-center space-x-2 pt-2">
                        <Switch id="status" v-model="isActive" />
                        <Label for="status" class="font-normal">
                            {{ isActive ? 'Active' : 'Inactive' }}
                        </Label>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="first_name">First Name <span class="text-destructive">*</span></Label>
                    <Input
                        id="first_name"
                        v-model="model.first_name"
                        type="text"
                        placeholder="Enter first name"
                    />
                    <p v-if="model.errors.first_name" class="text-sm text-destructive">
                        {{ model.errors.first_name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="last_name">Last Name <span class="text-destructive">*</span></Label>
                    <Input
                        id="last_name"
                        v-model="model.last_name"
                        type="text"
                        placeholder="Enter last name"
                    />
                    <p v-if="model.errors.last_name" class="text-sm text-destructive">
                        {{ model.errors.last_name }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        v-model="model.email"
                        type="email"
                        placeholder="Enter email address"
                    />
                    <p v-if="model.errors.email" class="text-sm text-destructive">
                        {{ model.errors.email }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="phone_number">Phone Number</Label>
                    <Input
                        id="phone_number"
                        v-model="model.phone_number"
                        type="text"
                        placeholder="Enter phone number"
                    />
                    <p v-if="model.errors.phone_number" class="text-sm text-destructive">
                        {{ model.errors.phone_number }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="gender">Gender</Label>
                    <Select v-model="selectedGender">
                        <SelectTrigger id="gender">
                            <SelectValue placeholder="Select gender" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in genderOptions"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="model.errors.gender" class="text-sm text-destructive">
                        {{ model.errors.gender }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="date_of_birth">Date of Birth</Label>
                    <Input
                        id="date_of_birth"
                        v-model="model.date_of_birth"
                        type="date"
                    />
                    <p v-if="model.errors.date_of_birth" class="text-sm text-destructive">
                        {{ model.errors.date_of_birth }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="birth_place">Birth Place</Label>
                    <Input
                        id="birth_place"
                        v-model="model.birth_place"
                        type="text"
                        placeholder="Enter birth place"
                    />
                    <p v-if="model.errors.birth_place" class="text-sm text-destructive">
                        {{ model.errors.birth_place }}
                    </p>
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <Label for="current_address">Current Address</Label>
                    <Textarea
                        id="current_address"
                        v-model="model.current_address"
                        placeholder="Enter current address"
                        rows="2"
                    />
                    <p v-if="model.errors.current_address" class="text-sm text-destructive">
                        {{ model.errors.current_address }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Employment Information Section -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Employment Information</h3>
                <p class="text-sm text-muted-foreground">Work-related details</p>
            </div>
            <Separator />

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="institution_id">Institution</Label>
                    <Select v-model="selectedInstitution">
                        <SelectTrigger id="institution_id">
                            <SelectValue placeholder="Select institution" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="institution in props.institutions"
                                :key="institution.id"
                                :value="institution.id.toString()"
                            >
                                {{ institution.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="model.errors.institution_id" class="text-sm text-destructive">
                        {{ model.errors.institution_id }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="department_id">Department</Label>
                    <Select v-model="selectedDepartment" :disabled="!model.institution_id">
                        <SelectTrigger id="department_id">
                            <SelectValue placeholder="Select department" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="department in props.departments"
                                :key="department.id"
                                :value="department.id.toString()"
                            >
                                {{ department.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="model.errors.department_id" class="text-sm text-destructive">
                        {{ model.errors.department_id }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="job_title">Job Title</Label>
                    <Input
                        id="job_title"
                        v-model="model.job_title"
                        type="text"
                        placeholder="Enter job title"
                    />
                    <p v-if="model.errors.job_title" class="text-sm text-destructive">
                        {{ model.errors.job_title }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="employee_type">Employee Type</Label>
                    <Select v-model="selectedEmployeeType">
                        <SelectTrigger id="employee_type">
                            <SelectValue placeholder="Select employee type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="type in props.employeeTypes"
                                :key="type.value"
                                :value="type.value"
                            >
                                {{ type.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="model.errors.employee_type" class="text-sm text-destructive">
                        {{ model.errors.employee_type }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="salary">Salary</Label>
                    <Input
                        id="salary"
                        v-model.number="salaryValue"
                        type="number"
                        min="0"
                        step="0.01"
                        placeholder="Enter salary"
                    />
                    <p v-if="model.errors.salary" class="text-sm text-destructive">
                        {{ model.errors.salary }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="hire_date">Hire Date</Label>
                    <Input
                        id="hire_date"
                        v-model="model.hire_date"
                        type="date"
                    />
                    <p v-if="model.errors.hire_date" class="text-sm text-destructive">
                        {{ model.errors.hire_date }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="probation_date">Probation Start Date</Label>
                    <Input
                        id="probation_date"
                        v-model="model.probation_date"
                        type="date"
                    />
                    <p v-if="model.errors.probation_date" class="text-sm text-destructive">
                        {{ model.errors.probation_date }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="probation_end_date">Probation End Date</Label>
                    <Input
                        id="probation_end_date"
                        v-model="model.probation_end_date"
                        type="date"
                    />
                    <p v-if="model.errors.probation_end_date" class="text-sm text-destructive">
                        {{ model.errors.probation_end_date }}
                    </p>
                </div>

            </div>
        </div>

        <!-- Certification Section -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Certification</h3>
                <p class="text-sm text-muted-foreground">Professional certifications and credentials</p>
            </div>
            <Separator />

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="certificate">Certificate Name</Label>
                    <Input
                        id="certificate"
                        v-model="model.certificate"
                        type="text"
                        placeholder="Enter certificate name"
                    />
                    <p v-if="model.errors.certificate" class="text-sm text-destructive">
                        {{ model.errors.certificate }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label for="certificate_code">Certificate Code</Label>
                    <Input
                        id="certificate_code"
                        v-model="model.certificate_code"
                        type="text"
                        placeholder="Enter certificate code"
                    />
                    <p v-if="model.errors.certificate_code" class="text-sm text-destructive">
                        {{ model.errors.certificate_code }}
                    </p>
                </div>

                <div class="space-y-2 sm:col-span-2">
                    <Label>Certificate Image</Label>
                    <ImageUpload
                        v-model="certificateImages"
                        label=""
                        :multiple="false"
                        :max-files="1"
                        :max-size="5"
                        :error="model.errors.certificate_image"
                    />
                </div>
            </div>
        </div>

        <!-- Avatar Section -->
        <div class="space-y-4">
            <div>
                <h3 class="text-sm font-medium">Profile Photo</h3>
                <p class="text-sm text-muted-foreground">Upload employee profile photo</p>
            </div>
            <Separator />

            <ImageUpload
                v-model="avatarImages"
                label=""
                :multiple="false"
                :max-files="1"
                :max-size="5"
                :error="model.errors.avatar_url"
            />
        </div>
    </div>
</template>
