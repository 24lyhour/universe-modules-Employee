<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { InertiaForm } from '@inertiajs/vue3';
import type { EmployeeFormData, SchoolOption, DepartmentOption, EmployeeTypeOption } from '../../../../types';

interface Props {
    form: InertiaForm<EmployeeFormData>;
    schools?: SchoolOption[];
    departments?: DepartmentOption[];
    employeeTypes?: EmployeeTypeOption[];
}

const props = withDefaults(defineProps<Props>(), {
    schools: () => [],
    departments: () => [],
    employeeTypes: () => [],
});

const emit = defineEmits<{
    schoolChange: [schoolId: number | null];
}>();

const selectedSchool = computed({
    get: () => props.form.school_id?.toString(),
    set: (value: string | undefined) => {
        const newValue = value ? parseInt(value) : null;
        props.form.school_id = newValue;
        props.form.department_id = null;
        emit('schoolChange', newValue);
    },
});

const selectedDepartment = computed({
    get: () => props.form.department_id?.toString(),
    set: (value: string | undefined) => {
        props.form.department_id = value ? parseInt(value) : null;
    },
});

const selectedEmployeeType = computed({
    get: () => props.form.employee_type || undefined,
    set: (value: string | undefined) => {
        props.form.employee_type = (value as 'full_time' | 'part_time' | 'contract' | 'intern') || null;
    },
});

const salaryValue = computed({
    get: () => props.form.salary ?? undefined,
    set: (value: number | undefined) => {
        props.form.salary = value ?? null;
    },
});
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <CardTitle class="text-base">Employment Information</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- School -->
                <div class="space-y-2">
                    <Label for="school_id">School</Label>
                    <Select v-model="selectedSchool">
                        <SelectTrigger id="school_id">
                            <SelectValue placeholder="Select school" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="school in schools" :key="school.id" :value="school.id.toString()">
                                {{ school.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.school_id" class="text-xs text-destructive">
                        {{ form.errors.school_id }}
                    </p>
                </div>

                <!-- Department -->
                <div class="space-y-2">
                    <Label for="department_id">Department</Label>
                    <Select v-model="selectedDepartment" :disabled="!form.school_id">
                        <SelectTrigger id="department_id">
                            <SelectValue placeholder="Select department" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="dept in departments" :key="dept.id" :value="dept.id.toString()">
                                {{ dept.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.department_id" class="text-xs text-destructive">
                        {{ form.errors.department_id }}
                    </p>
                </div>

                <!-- Job Title -->
                <div class="space-y-2">
                    <Label for="job_title">Job Title</Label>
                    <Input id="job_title" v-model="form.job_title" type="text" placeholder="Software Engineer" />
                    <p v-if="form.errors.job_title" class="text-xs text-destructive">
                        {{ form.errors.job_title }}
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
                            <SelectItem v-for="type in employeeTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.employee_type" class="text-xs text-destructive">
                        {{ form.errors.employee_type }}
                    </p>
                </div>

                <!-- Salary -->
                <div class="space-y-2">
                    <Label for="salary">Salary</Label>
                    <Input id="salary" v-model.number="salaryValue" type="number" min="0" step="0.01" placeholder="0.00" />
                    <p v-if="form.errors.salary" class="text-xs text-destructive">
                        {{ form.errors.salary }}
                    </p>
                </div>

                <!-- Hire Date -->
                <div class="space-y-2">
                    <Label for="hire_date">Hire Date</Label>
                    <Input id="hire_date" v-model="form.hire_date" type="date" />
                    <p v-if="form.errors.hire_date" class="text-xs text-destructive">
                        {{ form.errors.hire_date }}
                    </p>
                </div>

                <!-- Probation Start -->
                <div class="space-y-2">
                    <Label for="probation_date">Probation Start</Label>
                    <Input id="probation_date" v-model="form.probation_date" type="date" />
                    <p v-if="form.errors.probation_date" class="text-xs text-destructive">
                        {{ form.errors.probation_date }}
                    </p>
                </div>

                <!-- Probation End -->
                <div class="space-y-2">
                    <Label for="probation_end_date">Probation End</Label>
                    <Input id="probation_end_date" v-model="form.probation_end_date" type="date" />
                    <p v-if="form.errors.probation_end_date" class="text-xs text-destructive">
                        {{ form.errors.probation_end_date }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
