<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TableReusable, StatsCard } from '@/components/shared';
import type { TableColumn, TableAction, PaginationData } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    Plus,
    Users,
    CheckCircle,
    Clock,
    XCircle,
    Search,
    Eye,
    Pencil,
    Trash2,
    QrCode,
    CalendarDays,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { AttendanceIndexProps, Attendance } from '@employee/types';

const props = defineProps<AttendanceIndexProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/dashboard/attendances' },
];

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || 'all');
const employeeFilter = ref(props.filters.employee_id?.toString() || 'all');
const departmentFilter = ref(props.filters.department_id?.toString() || 'all');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');

const getInitials = (name: string | null) => {
    if (!name) return '?';
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const getStatusVariant = (status: string): 'default' | 'secondary' | 'destructive' | 'outline' => {
    switch (status) {
        case 'present':
            return 'default';
        case 'late':
        case 'early_leave':
            return 'secondary';
        case 'absent':
            return 'destructive';
        default:
            return 'outline';
    }
};

const columns: TableColumn<Attendance>[] = [
    {
        key: 'employee',
        label: 'Employee',
        render: (attendance) => attendance.employee_name || '-',
    },
    {
        key: 'attendance_date_formatted',
        label: 'Date',
        render: (attendance) => attendance.attendance_date_formatted,
    },
    {
        key: 'check_in_time',
        label: 'Check In',
        render: (attendance) => attendance.check_in_time || '-',
    },
    {
        key: 'check_out_time',
        label: 'Check Out',
        render: (attendance) => attendance.check_out_time || '-',
    },
    {
        key: 'work_hours_formatted',
        label: 'Work Hours',
        render: (attendance) => attendance.work_hours_formatted,
    },
    {
        key: 'status',
        label: 'Status',
        render: (attendance) => attendance.status_label,
    },
    {
        key: 'check_in_method_label',
        label: 'Method',
        render: (attendance) => attendance.check_in_method_label,
    },
];

const actions: TableAction<Attendance>[] = [
    {
        label: 'View',
        icon: Eye,
        onClick: (attendance) => router.visit(`/dashboard/attendances/${attendance.id}`),
    },
    {
        label: 'Edit',
        icon: Pencil,
        onClick: (attendance) => router.visit(`/dashboard/attendances/${attendance.id}/edit`),
    },
    {
        label: 'Delete',
        icon: Trash2,
        onClick: (attendance) => {
            if (confirm('Are you sure you want to delete this attendance record?')) {
                router.delete(`/dashboard/attendances/${attendance.id}`);
            }
        },
        variant: 'destructive',
        separator: true,
    },
];

const pagination = computed<PaginationData>(() => ({
    current_page: props.attendances.meta.current_page,
    last_page: props.attendances.meta.last_page,
    per_page: props.attendances.meta.per_page,
    total: props.attendances.meta.total,
}));

const getFilterParams = () => ({
    search: search.value || undefined,
    status: statusFilter.value !== 'all' ? statusFilter.value : undefined,
    employee_id: employeeFilter.value !== 'all' ? employeeFilter.value : undefined,
    department_id: departmentFilter.value !== 'all' ? departmentFilter.value : undefined,
    date_from: dateFrom.value || undefined,
    date_to: dateTo.value || undefined,
});

const handlePageChange = (page: number) => {
    router.get('/dashboard/attendances', {
        page,
        per_page: pagination.value.per_page,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handlePerPageChange = (perPage: number) => {
    router.get('/dashboard/attendances', {
        per_page: perPage,
        ...getFilterParams(),
    }, { preserveState: true });
};

const handleSearch = () => {
    router.get('/dashboard/attendances', getFilterParams(), { preserveState: true });
};

watch([statusFilter, employeeFilter, departmentFilter, dateFrom, dateTo], () => {
    router.get('/dashboard/attendances', getFilterParams(), { preserveState: true });
});

const handleCreate = () => {
    router.visit('/dashboard/attendances/create');
};

const handleOpenScanner = () => {
    router.visit('/dashboard/attendances/scanner');
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Attendance" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-5">
                <StatsCard
                    title="Total Employees"
                    :value="props.stats.total_employees"
                    :icon="Users"
                />
                <StatsCard
                    title="Present Today"
                    :value="props.stats.present_today"
                    :icon="CheckCircle"
                    variant="success"
                />
                <StatsCard
                    title="Late Today"
                    :value="props.stats.late_today"
                    :icon="Clock"
                    variant="warning"
                />
                <StatsCard
                    title="Absent Today"
                    :value="props.stats.absent_today"
                    :icon="XCircle"
                    variant="danger"
                />
                <StatsCard
                    title="On Leave"
                    :value="props.stats.on_leave_today"
                    :icon="CalendarDays"
                    variant="info"
                />
            </div>

            <!-- Main Content -->
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Attendance Records</h2>
                        <p class="text-sm text-muted-foreground">Track employee attendance</p>
                    </div>
                    <div class="flex gap-2">
                        <Button variant="outline" @click="handleOpenScanner">
                            <QrCode class="mr-2 h-4 w-4" />
                            QR Scanner
                        </Button>
                        <Button @click="handleCreate">
                            <Plus class="mr-2 h-4 w-4" />
                            Manual Entry
                        </Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-4">
                    <div class="relative flex-1 min-w-[200px] max-w-sm">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input
                            v-model="search"
                            placeholder="Search by employee..."
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
                            <SelectItem
                                v-for="(label, value) in props.statuses"
                                :key="value"
                                :value="value"
                            >
                                {{ label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select v-model="departmentFilter">
                        <SelectTrigger class="w-[180px]">
                            <SelectValue placeholder="Department" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">All Departments</SelectItem>
                            <SelectItem
                                v-for="dept in props.departmentOptions"
                                :key="dept.id"
                                :value="dept.id.toString()"
                            >
                                {{ dept.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Input
                        v-model="dateFrom"
                        type="date"
                        class="w-[150px]"
                        placeholder="From"
                    />
                    <Input
                        v-model="dateTo"
                        type="date"
                        class="w-[150px]"
                        placeholder="To"
                    />
                </div>

                <!-- Table -->
                <TableReusable
                    :data="props.attendances.data"
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
                                <AvatarImage :src="item.employee_avatar || ''" :alt="item.employee_name || ''" />
                                <AvatarFallback>{{ getInitials(item.employee_name) }}</AvatarFallback>
                            </Avatar>
                            <div>
                                <p class="font-medium">{{ item.employee_name }}</p>
                                <p class="text-sm text-muted-foreground">{{ item.employee_code }}</p>
                            </div>
                        </div>
                    </template>
                    <template #cell-status="{ item }">
                        <Badge :variant="getStatusVariant(item.status)">
                            {{ item.status_label }}
                        </Badge>
                    </template>
                    <template #cell-check_in_method_label="{ item }">
                        <Badge variant="outline">
                            {{ item.check_in_method_label }}
                        </Badge>
                    </template>
                </TableReusable>
            </div>
        </div>
    </AppLayout>
</template>
