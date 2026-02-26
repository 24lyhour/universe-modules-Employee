<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TableReusable, StatsCard } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Switch } from '@/components/ui/switch';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Plus, Tags, CheckCircle, XCircle, Search, Eye, Pencil, Trash2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { EmployeeTypeIndexProps, EmployeeTypeModel } from '@employee/types';

const props = defineProps<EmployeeTypeIndexProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Employee Types', href: '/dashboard/employee-types' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');

const columns: TableColumn<EmployeeTypeModel>[] = [
    {
        key: 'name',
        label: 'Name',
        render: (type) => type.name,
    },
    {
        key: 'description',
        label: 'Description',
        render: (type) => type.description || '-',
    },
    {
        key: 'employees_count',
        label: 'Employees',
        render: (type) => type.employees_count?.toString() || '0',
    },
    {
        key: 'status',
        label: 'Status',
        render: (type) => type.status ? 'Active' : 'Inactive',
    },
];

const actions: TableAction<EmployeeTypeModel>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (type) => router.visit(`/dashboard/employee-types/${type.uuid}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (type) => router.visit(`/dashboard/employee-types/${type.uuid}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (type) => router.visit(`/dashboard/employee-types/${type.uuid}/delete`),
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.employeeTypes.meta.current_page,
    last_page: props.employeeTypes.meta.last_page,
    per_page: props.employeeTypes.meta.per_page,
    total: props.employeeTypes.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/employee-types', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/employee-types', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/employee-types', getFilterParams(), { preserveState: true });
};

watch([statusFilter], () => {
    router.get('/dashboard/employee-types', getFilterParams(), { preserveState: true });
});

const handleCreate = () => {
    router.visit('/dashboard/employee-types/create');
};

const handleStatusToggle = (type: EmployeeTypeModel, newStatus: boolean) => {
    router.put(`/dashboard/employee-types/${type.uuid}/toggle-status`, {
        status: newStatus,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Employee Types" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <StatsCard
                    title="Total Types"
                    :value="props.stats.total"
                    :icon="Tags"
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
                        <h2 class="text-lg font-semibold">Employee Types</h2>
                        <p class="text-sm text-muted-foreground">Manage employee type categories</p>
                    </div>
                    <Button @click="handleCreate">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Type
                    </Button>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Search employee types..."
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
                </div>

                <!-- Table -->
                <TableReusable
                    :data="props.employeeTypes.data"
                    :columns="columns"
                    :actions="actions"
                    :pagination="pagination"
                    :searchable="false"
                    @page-change="handlePageChange"
                    @per-page-change="handlePerPageChange"
                >
                    <template #cell-name="{ item }">
                        <div class="font-medium">{{ item.name }}</div>
                    </template>
                    <template #cell-employees_count="{ item }">
                        <Badge variant="secondary">
                            {{ item.employees_count || 0 }} employees
                        </Badge>
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
