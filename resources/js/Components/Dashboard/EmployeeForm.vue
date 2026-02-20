<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
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
    form: InertiaForm<EmployeeFormData>;
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

// Computed for institution select
const selectedInstitution = computed({
    get: () => props.form.institution_id?.toString(),
    set: (value: string | undefined) => {
        const newValue = value ? parseInt(value) : null;
        props.form.institution_id = newValue;
        props.form.department_id = null;
        emit('institutionChange', newValue);
    },
});

// Computed for department select
const selectedDepartment = computed({
    get: () => props.form.department_id?.toString(),
    set: (value: string | undefined) => {
        props.form.department_id = value ? parseInt(value) : null;
    },
});

// Computed for gender select
const selectedGender = computed({
    get: () => props.form.gender || undefined,
    set: (value: string | undefined) => {
        props.form.gender = (value as 'male' | 'female' | 'other') || null;
    },
});

// Computed for employee type select
const selectedEmployeeType = computed({
    get: () => props.form.employee_type || undefined,
    set: (value: string | undefined) => {
        props.form.employee_type = (value as 'full_time' | 'part_time' | 'contract' | 'intern') || null;
    },
});

// Convert avatar_url string to array for ImageUpload component
const avatarImages = computed({
    get: () => props.form.avatar_url ? [props.form.avatar_url] : [],
    set: (value: string[]) => {
        props.form.avatar_url = value.length > 0 ? value[0] : '';
    },
});

// Convert certificate_image string to array for ImageUpload component
const certificateImages = computed({
    get: () => props.form.certificate_image ? [props.form.certificate_image] : [],
    set: (value: string[]) => {
        props.form.certificate_image = value.length > 0 ? value[0] : '';
    },
});

// Computed for salary (convert null to undefined for v-model.number)
const salaryValue = computed({
    get: () => props.form.salary ?? undefined,
    set: (value: number | undefined) => {
        props.form.salary = value ?? null;
    },
});

// Status switch
const isActive = computed({
    get: () => props.form.status,
    set: (value: boolean) => {
        props.form.status = value;
    },
});

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' },
];
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column: Form Fields -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Basic Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Basic Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Employee Code -->
                        <div class="space-y-2">
                            <Label for="employee_code">Employee Code <span class="text-destructive">*</span></Label>
                            <Input
                                id="employee_code"
                                v-model="props.form.employee_code"
                                type="text"
                                placeholder="EMP-000001"
                                :disabled="mode === 'edit'"
                            />
                            <p v-if="props.form.errors.employee_code" class="text-xs text-destructive">
                                {{ props.form.errors.employee_code }}
                            </p>
                        </div>

                        <!-- First Name -->
                        <div class="space-y-2">
                            <Label for="first_name">First Name <span class="text-destructive">*</span></Label>
                            <Input
                                id="first_name"
                                v-model="props.form.first_name"
                                type="text"
                                placeholder="John"
                            />
                            <p v-if="props.form.errors.first_name" class="text-xs text-destructive">
                                {{ props.form.errors.first_name }}
                            </p>
                        </div>

                        <!-- Last Name -->
                        <div class="space-y-2">
                            <Label for="last_name">Last Name <span class="text-destructive">*</span></Label>
                            <Input
                                id="last_name"
                                v-model="props.form.last_name"
                                type="text"
                                placeholder="Doe"
                            />
                            <p v-if="props.form.errors.last_name" class="text-xs text-destructive">
                                {{ props.form.errors.last_name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="props.form.email"
                                type="email"
                                placeholder="john.doe@example.com"
                            />
                            <p v-if="props.form.errors.email" class="text-xs text-destructive">
                                {{ props.form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone Number -->
                        <div class="space-y-2">
                            <Label for="phone_number">Phone Number</Label>
                            <Input
                                id="phone_number"
                                v-model="props.form.phone_number"
                                type="text"
                                placeholder="+1 234 567 8900"
                            />
                            <p v-if="props.form.errors.phone_number" class="text-xs text-destructive">
                                {{ props.form.errors.phone_number }}
                            </p>
                        </div>

                        <!-- Gender -->
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
                            <p v-if="props.form.errors.gender" class="text-xs text-destructive">
                                {{ props.form.errors.gender }}
                            </p>
                        </div>

                        <!-- Date of Birth -->
                        <div class="space-y-2">
                            <Label for="date_of_birth">Date of Birth</Label>
                            <Input
                                id="date_of_birth"
                                v-model="props.form.date_of_birth"
                                type="date"
                            />
                            <p v-if="props.form.errors.date_of_birth" class="text-xs text-destructive">
                                {{ props.form.errors.date_of_birth }}
                            </p>
                        </div>

                        <!-- Birth Place -->
                        <div class="space-y-2 sm:col-span-2">
                            <Label for="birth_place">Birth Place</Label>
                            <Input
                                id="birth_place"
                                v-model="props.form.birth_place"
                                type="text"
                                placeholder="City, Country"
                            />
                            <p v-if="props.form.errors.birth_place" class="text-xs text-destructive">
                                {{ props.form.errors.birth_place }}
                            </p>
                        </div>
                    </div>

                    <!-- Current Address (Full Width) -->
                    <div class="space-y-2">
                        <Label for="current_address">Current Address</Label>
                        <Textarea
                            id="current_address"
                            v-model="props.form.current_address"
                            placeholder="Street address, City, State, ZIP Code"
                            rows="2"
                        />
                        <p v-if="props.form.errors.current_address" class="text-xs text-destructive">
                            {{ props.form.errors.current_address }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Employment Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Employment Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Institution -->
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
                            <p v-if="props.form.errors.institution_id" class="text-xs text-destructive">
                                {{ props.form.errors.institution_id }}
                            </p>
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <Label for="department_id">Department</Label>
                            <Select v-model="selectedDepartment" :disabled="!props.form.institution_id">
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
                            <p v-if="props.form.errors.department_id" class="text-xs text-destructive">
                                {{ props.form.errors.department_id }}
                            </p>
                        </div>

                        <!-- Job Title -->
                        <div class="space-y-2">
                            <Label for="job_title">Job Title</Label>
                            <Input
                                id="job_title"
                                v-model="props.form.job_title"
                                type="text"
                                placeholder="Software Engineer"
                            />
                            <p v-if="props.form.errors.job_title" class="text-xs text-destructive">
                                {{ props.form.errors.job_title }}
                            </p>
                        </div>

                        <!-- Employee Type -->
                        <div class="space-y-2">
                            <Label for="employee_type">Employee Type</Label>
                            <Select v-model="selectedEmployeeType">
                                <SelectTrigger id="employee_type">
                                    <SelectValue placeholder="Select type" />
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
                            <p v-if="props.form.errors.employee_type" class="text-xs text-destructive">
                                {{ props.form.errors.employee_type }}
                            </p>
                        </div>

                        <!-- Salary -->
                        <div class="space-y-2">
                            <Label for="salary">Salary</Label>
                            <Input
                                id="salary"
                                v-model.number="salaryValue"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                            />
                            <p v-if="props.form.errors.salary" class="text-xs text-destructive">
                                {{ props.form.errors.salary }}
                            </p>
                        </div>

                        <!-- Hire Date -->
                        <div class="space-y-2">
                            <Label for="hire_date">Hire Date</Label>
                            <Input
                                id="hire_date"
                                v-model="props.form.hire_date"
                                type="date"
                            />
                            <p v-if="props.form.errors.hire_date" class="text-xs text-destructive">
                                {{ props.form.errors.hire_date }}
                            </p>
                        </div>

                        <!-- Probation Start Date -->
                        <div class="space-y-2">
                            <Label for="probation_date">Probation Start</Label>
                            <Input
                                id="probation_date"
                                v-model="props.form.probation_date"
                                type="date"
                            />
                            <p v-if="props.form.errors.probation_date" class="text-xs text-destructive">
                                {{ props.form.errors.probation_date }}
                            </p>
                        </div>

                        <!-- Probation End Date -->
                        <div class="space-y-2">
                            <Label for="probation_end_date">Probation End</Label>
                            <Input
                                id="probation_end_date"
                                v-model="props.form.probation_end_date"
                                type="date"
                            />
                            <p v-if="props.form.errors.probation_end_date" class="text-xs text-destructive">
                                {{ props.form.errors.probation_end_date }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Certification Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Certification</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Certificate Name -->
                        <div class="space-y-2">
                            <Label for="certificate">Certificate Name</Label>
                            <Input
                                id="certificate"
                                v-model="props.form.certificate"
                                type="text"
                                placeholder="Bachelor's Degree"
                            />
                            <p v-if="props.form.errors.certificate" class="text-xs text-destructive">
                                {{ props.form.errors.certificate }}
                            </p>
                        </div>

                        <!-- Certificate Code -->
                        <div class="space-y-2">
                            <Label for="certificate_code">Certificate Code</Label>
                            <Input
                                id="certificate_code"
                                v-model="props.form.certificate_code"
                                type="text"
                                placeholder="CERT-2024-001"
                            />
                            <p v-if="props.form.errors.certificate_code" class="text-xs text-destructive">
                                {{ props.form.errors.certificate_code }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Right Column: Profile Photo, Status & Certificate -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Profile Photo Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Profile Photo</CardTitle>
                </CardHeader>
                <CardContent>
                    <ImageUpload
                        v-model="avatarImages"
                        label=""
                        :multiple="false"
                        :max-files="1"
                        :max-size="5"
                        :error="props.form.errors.avatar_url"
                    />
                </CardContent>
            </Card>

            <!-- Status Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Status</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">Employee Status</p>
                            <p class="text-xs text-muted-foreground">
                                {{ isActive ? 'Employee is active' : 'Employee is inactive' }}
                            </p>
                        </div>
                        <Switch v-model="isActive" />
                    </div>
                </CardContent>
            </Card>

            <!-- Certificate Image Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Certificate Document</CardTitle>
                </CardHeader>
                <CardContent>
                    <ImageUpload
                        v-model="certificateImages"
                        label=""
                        :multiple="false"
                        :max-files="1"
                        :max-size="5"
                        :error="props.form.errors.certificate_image"
                    />
                </CardContent>
            </Card>
        </div>
    </div>
</template>
