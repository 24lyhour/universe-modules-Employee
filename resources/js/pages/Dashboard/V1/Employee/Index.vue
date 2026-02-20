<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TableReusable, StatsCard } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Plus, Users, CheckCircle, XCircle, Search, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { EmployeeIndexProps, Employee } from '@employee/types';

const props = defineProps<EmployeeIndexProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Employees', href: '/dashboard/employees' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const employeeTypeFilter = ref(props.filters.employee_type || 'all');
const schoolFilter = ref(props.filters.school_id || 'all');

const getInitials = (name: string) => {
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const columns: TableColumn<Employee>[] = [
    {
        key: 'employee',
        label: 'Employee',
        render: (employee) => employee.full_name,
    },
    {
        key: 'employee_code',
        label: 'Code',
        render: (employee) => employee.employee_code,
    },
    {
        key: 'email',
        label: 'Email',
        render: (employee) => employee.email || '-',
    },
    {
        key: 'employee_type_label',
        label: 'Type',
        render: (employee) => employee.employee_type_label || '-',
    },
    {
        key: 'school_name',
        label: 'School',
        render: (employee) => employee.school_name || '-',
    },
    {
        key: 'department_name',
        label: 'Department',
        render: (employee) => employee.department_name || '-',
    },
    {
        key: 'status',
        label: 'Status',
        render: (employee) => employee.status ? 'Active' : 'Inactive',
    },
];

const actions: TableAction<Employee>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (employee) => router.visit(`/dashboard/employees/${employee.id}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (employee) => router.visit(`/dashboard/employees/${employee.id}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (employee) => router.visit(`/dashboard/employees/${employee.id}/delete`),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.employees.meta.current_page,
    last_page: props.employees.meta.last_page,
    per_page: props.employees.meta.per_page,
    total: props.employees.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    employee_type: employeeTypeFilter.value !== 'all' ? employeeTypeFilter.value : undefined,
    school_id: schoolFilter.value !== 'all' ? schoolFilter.value : undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/employees', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/employees', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/employees', getFilterParams(), { preserveState: true });
};

watch([statusFilter, employeeTypeFilter, schoolFilter], () => {
    router.get('/dashboard/employees', getFilterParams(), { preserveState: true });
});

const handleCreate = () => {
    router.visit('/dashboard/employees/create');
};

const handleStatusToggle = (employee: Employee, newStatus: boolean) => {
    router.put(`/dashboard/employees/${employee.id}/toggle-status`, {
        status: newStatus,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Employees" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatsCard
                    title="Total Employees"
                    :value="props.stats.total"
                    :icon="Users"
                />
                <StatsCard
                    title="Active"
                    :value="props.stats.active"
                    :icon="CheckCircle"
                    variant="success"
                />
                <StatsCard
                    title="Inactive"
                    :value="props.stats.inactive"
                    :icon="XCircle"
                    variant="warning"
                />
            </div>

            <!-- Main Content -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Employees</h2>
                        <p class="text-sm text-muted-foreground">Manage your employees</p>
                    </div>
                    <Button @click="handleCreate">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Employee
                    </Button>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Search employees..."
                            class="pl-9"
                            @keyup.enter="handleSearch"
                        />
                    </div>
                    <Select v-model="statusFilter">
                        <SelectTrigger class="w-[150px]">
                            <SelectValue placeholder="Status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Status</SelectItem>
                            <SelectItem value="1">Active</SelectItem>
                            <SelectItem value="0">Inactive</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="employeeTypeFilter">
                        <SelectTrigger class="w-[150px]">
                            <SelectValue placeholder="Type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Types</SelectItem>
                            <SelectItem value="full_time">Full Time</SelectItem>
                            <SelectItem value="part_time">Part Time</SelectItem>
                            <SelectItem value="contract">Contract</SelectItem>
                            <SelectItem value="intern">Intern</SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="schoolFilter">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="School" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Schools</SelectItem>
                            <SelectItem
                                v-for="school in props.schools"
                                :key="school.id"
                                :value="school.id.toString()"
                            >
                                {{ school.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Table -->
                <TableReusable
                    :data="props.employees.data"
                    :columns="columns"
                    :actions="actions"
                    :pagination="pagination"
                    :searchable="false"
                    @page-change="handlePageChange"
                    @per-page-change="handlePerPageChange"
                >
                    <template #cell-employee="{ item }">
                        <div class="flex items-center gap-3">
                            <Avatar class="h-9 w-9">
                                <AvatarImage :src="item.avatar_url || ''" :alt="item.full_name" />
                                <AvatarFallback>{{ getInitials(item.full_name) }}</AvatarFallback>
                            </Avatar>
                            <div>
                                <p class="font-medium">{{ item.full_name }}</p>
                                <p class="text-sm text-muted-foreground">{{ item.job_title || '-' }}</p>
                            </div>
                        </div>
                    </template>
                    <template #cell-employee_type_label="{ item }">
                        <Badge v-if="item.employee_type_label" variant="outline">
                            {{ item.employee_type_label }}
                        </Badge>
                        <span v-else class="text-muted-foreground">-</span>
                    </template>
                    <template #cell-status="{ item }">
                        <div class="flex items-center gap-2" @click.stop>
                            <Switch
                                :model-value="item.status"
                                @update:model-value="handleStatusToggle(item, $event)"
                            />
                            <span class="text-sm text-muted-foreground">
                                {{ item.status ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </template>
                </TableReusable>
            </div>
        </div>
    </AppLayout>
</template>
