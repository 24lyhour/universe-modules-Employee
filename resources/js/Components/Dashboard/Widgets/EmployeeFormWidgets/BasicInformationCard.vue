<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { InertiaForm } from '@inertiajs/vue3';
import type { EmployeeFormData, MaritalStatusOption } from '../../../../types';

interface Props {
    form: InertiaForm<EmployeeFormData>;
    mode?: 'create' | 'edit';
    maritalStatuses?: MaritalStatusOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    maritalStatuses: () => [
        { value: 'single', label: 'Single' },
        { value: 'married', label: 'Married' },
    ],
});

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' },
];

const selectedGender = computed({
    get: () => props.form.gender || undefined,
    set: (value: string | undefined) => {
        props.form.gender = (value as 'male' | 'female' | 'other') || null;
    },
});

const selectedMaritalStatus = computed({
    get: () => props.form.marital_status || undefined,
    set: (value: string | undefined) => {
        props.form.marital_status = (value as 'single' | 'married') || null;
        if (value === 'single') {
            props.form.family_members = props.form.family_members.filter(
                (m) => m.relationship !== 'spouse'
            );
        }
    },
});
</script>

<template>
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
                        v-model="form.employee_code"
                        type="text"
                        placeholder="EMP-000001"
                        :disabled="mode === 'edit'"
                    />
                    <p v-if="form.errors.employee_code" class="text-xs text-destructive">
                        {{ form.errors.employee_code }}
                    </p>
                </div>

                <!-- First Name -->
                <div class="space-y-2">
                    <Label for="first_name">First Name <span class="text-destructive">*</span></Label>
                    <Input id="first_name" v-model="form.first_name" type="text" placeholder="John" />
                    <p v-if="form.errors.first_name" class="text-xs text-destructive">
                        {{ form.errors.first_name }}
                    </p>
                </div>

                <!-- Last Name -->
                <div class="space-y-2">
                    <Label for="last_name">Last Name <span class="text-destructive">*</span></Label>
                    <Input id="last_name" v-model="form.last_name" type="text" placeholder="Doe" />
                    <p v-if="form.errors.last_name" class="text-xs text-destructive">
                        {{ form.errors.last_name }}
                    </p>
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <Label for="email">Email</Label>
                    <Input id="email" v-model="form.email" type="email" placeholder="john.doe@example.com" />
                    <p v-if="form.errors.email" class="text-xs text-destructive">
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Phone Number -->
                <div class="space-y-2">
                    <Label for="phone_number">Phone Number</Label>
                    <Input id="phone_number" v-model="form.phone_number" type="text" placeholder="+1 234 567 8900" />
                    <p v-if="form.errors.phone_number" class="text-xs text-destructive">
                        {{ form.errors.phone_number }}
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
                            <SelectItem v-for="opt in genderOptions" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.gender" class="text-xs text-destructive">
                        {{ form.errors.gender }}
                    </p>
                </div>

                <!-- Date of Birth -->
                <div class="space-y-2">
                    <Label for="date_of_birth">Date of Birth</Label>
                    <Input id="date_of_birth" v-model="form.date_of_birth" type="date" />
                    <p v-if="form.errors.date_of_birth" class="text-xs text-destructive">
                        {{ form.errors.date_of_birth }}
                    </p>
                </div>

                <!-- Birth Place -->
                <div class="space-y-2">
                    <Label for="birth_place">Birth Place</Label>
                    <Input id="birth_place" v-model="form.birth_place" type="text" placeholder="City, Country" />
                    <p v-if="form.errors.birth_place" class="text-xs text-destructive">
                        {{ form.errors.birth_place }}
                    </p>
                </div>

                <!-- Marital Status -->
                <div class="space-y-2">
                    <Label for="marital_status">Marital Status</Label>
                    <Select v-model="selectedMaritalStatus">
                        <SelectTrigger id="marital_status">
                            <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="opt in maritalStatuses" :key="opt.value" :value="opt.value">
                                {{ opt.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <p v-if="form.errors.marital_status" class="text-xs text-destructive">
                        {{ form.errors.marital_status }}
                    </p>
                </div>

                <!-- Ethnicity -->
                <div class="space-y-2">
                    <Label for="ethnicity">Ethnicity</Label>
                    <Input id="ethnicity" v-model="form.ethnicity" type="text" placeholder="Ethnicity" />
                    <p v-if="form.errors.ethnicity" class="text-xs text-destructive">
                        {{ form.errors.ethnicity }}
                    </p>
                </div>
            </div>

            <!-- Current Address -->
            <div class="space-y-2">
                <Label for="current_address">Current Address</Label>
                <TiptapEditor
                    v-model="form.current_address"
                    placeholder="Street address, City, State, ZIP Code..."
                    min-height="100px"
                    max-height="250px"
                />
                <p v-if="form.errors.current_address" class="text-xs text-destructive">
                    {{ form.errors.current_address }}
                </p>
            </div>
        </CardContent>
    </Card>
</template>
