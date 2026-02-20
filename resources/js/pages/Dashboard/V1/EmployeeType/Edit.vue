<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import EmployeeTypeForm from '@employee/Components/Dashboard/EmployeeTypeForm.vue';
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
import { toast } from 'vue-sonner';
import { employeeTypeSchema } from '@employee/validation/employeeTypeSchema';
import { useFormValidation } from '@/composables/useFormValidation';
import { ChevronLeft } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { EmployeeTypeFormData, EmployeeTypeEditProps } from '@employee/types';

const props = defineProps<EmployeeTypeEditProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Employee Types', href: '/dashboard/employee-types' },
    { title: props.employeeType.name, href: `/dashboard/employee-types/${props.employeeType.id}` },
    { title: 'Edit', href: `/dashboard/employee-types/${props.employeeType.id}/edit` },
];

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
                router.visit(`/dashboard/employee-types/${props.employeeType.id}`);
            },
        });
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Edit ${employeeType.name}`" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Link href="/dashboard/employee-types" class="text-muted-foreground hover:text-foreground">
                    <ChevronLeft class="h-5 w-5" />
                </Link>
                <div>
                    <h1 class="text-xl font-semibold">Edit Employee Type</h1>
                    <p class="text-sm text-muted-foreground">{{ employeeType.name }}</p>
                </div>
            </div>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-6">
                <EmployeeTypeForm :form="form" mode="edit" />

                <!-- Actions at Bottom -->
                <div class="flex justify-end gap-3 pt-4">
                    <Button type="button" variant="outline" as-child>
                        <Link :href="`/dashboard/employee-types/${employeeType.id}`">Cancel</Link>
                    </Button>
                    <Button type="submit" :disabled="isFormInvalid || form.processing">
                        {{ form.processing ? 'Saving...' : 'Save Changes' }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
