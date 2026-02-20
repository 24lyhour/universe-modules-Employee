<script setup lang="ts">
import { ModalForm } from '@/components/shared';
import { useForm } from '@inertiajs/vue3';
import { useModal } from 'momentum-modal';
import { computed, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { employeeTypeSchema } from '@employee/validation/employeeTypeSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import type { EmployeeTypeFormData, EmployeeTypeEditProps } from '@employee/types';

const props = defineProps<EmployeeTypeEditProps>();

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

const form = useForm<EmployeeTypeFormData>({
    name: props.employeeType.name,
    description: props.employeeType.description || '',
    time_start: props.employeeType.time_start || '',
    time_end: props.employeeType.time_end || '',
    status: props.employeeType.status,
});

const { validateForm, validateAndSubmit, createIsFormInvalid } = useFormValidation(
    employeeTypeSchema,
    ['name']
);

const getFormData = () => ({
    name: form.name,
    description: form.description || null,
    time_start: form.time_start || null,
    time_end: form.time_end || null,
    status: form.status,
});

watch(() => form.name, () => validateForm(getFormData()));

const isFormInvalid = createIsFormInvalid(getFormData);

const handleSubmit = () => {
    validateAndSubmit(getFormData(), form, () => {
        form.put(`/dashboard/employee-types/${props.employeeType.id}`, {
            onSuccess: () => {
                toast.success('Employee type updated successfully.');
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

const isActive = computed({
    get: () => form.status,
    set: (value: boolean) => {
        form.status = value;
    },
});
</script>

<template>
    <ModalForm
        v-model:open="isOpen"
        title="Edit Employee Type"
        :description="`Editing: ${employeeType.name}`"
        mode="edit"
        size="lg"
        submit-text="Save Changes"
        :loading="form.processing"
        :disabled="isFormInvalid"
        @submit="handleSubmit"
        @cancel="handleCancel"
    >
        <div class="space-y-4">
            <!-- Name -->
            <div class="space-y-2">
                <Label for="name">
                    Name <span class="text-destructive">*</span>
                </Label>
                <Input
                    id="name"
                    v-model="form.name"
                    placeholder="Enter type name"
                    :class="{ 'border-destructive': form.errors.name }"
                />
                <p v-if="form.errors.name" class="text-xs text-destructive">
                    {{ form.errors.name }}
                </p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <Label for="description">Description</Label>
                <Textarea
                    id="description"
                    v-model="form.description"
                    placeholder="Enter type description"
                    rows="3"
                    :class="{ 'border-destructive': form.errors.description }"
                />
                <p v-if="form.errors.description" class="text-xs text-destructive">
                    {{ form.errors.description }}
                </p>
            </div>

            <!-- Time Range -->
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="time_start">Start Time</Label>
                    <Input
                        id="time_start"
                        type="time"
                        v-model="form.time_start"
                        :class="{ 'border-destructive': form.errors.time_start }"
                    />
                    <p v-if="form.errors.time_start" class="text-xs text-destructive">
                        {{ form.errors.time_start }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="time_end">End Time</Label>
                    <Input
                        id="time_end"
                        type="time"
                        v-model="form.time_end"
                        :class="{ 'border-destructive': form.errors.time_end }"
                    />
                    <p v-if="form.errors.time_end" class="text-xs text-destructive">
                        {{ form.errors.time_end }}
                    </p>
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center justify-between rounded-lg border p-4">
                <div>
                    <p class="text-sm font-medium">Active Status</p>
                    <p class="text-xs text-muted-foreground">
                        {{ isActive ? 'Type is active' : 'Type is inactive' }}
                    </p>
                </div>
                <Switch v-model="isActive" />
            </div>
        </div>
    </ModalForm>
</template>
