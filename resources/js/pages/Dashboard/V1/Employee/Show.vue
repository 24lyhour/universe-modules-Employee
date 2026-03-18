<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    ArrowLeft,
    Pencil,
    Mail,
    Phone,
    MapPin,
    Building2,
    Calendar,
    Award,
    Banknote,
    QrCode,
    Clock,
    CheckCircle,
    AlertTriangle,
    XCircle,
    TrendingUp,
    Key,
    MoreVertical,
    UserPlus,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { EmployeeShowProps } from '@employee/types';
import Account from '@employee/Components/Dashboard/Account.vue';

const props = defineProps<EmployeeShowProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Employees', href: '/dashboard/employees' },
    { title: props.employee.full_name, href: `/dashboard/employees/${props.employee.uuid}` },
];

const getInitials = (name: string) => {
    return name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2);
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const formatCurrency = (value: number | null) => {
    if (value === null) return '-';
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value);
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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="employee.full_name" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" as-child>
                    <Link href="/dashboard/employees"><ArrowLeft class="h-4 w-4" /></Link>
                </Button>
                <h1 class="text-xl font-semibold">Employee Profile</h1>
            </div>

            <!-- Profile Card -->
            <div class="rounded-xl border bg-card">
                <!-- Profile Header -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 p-6 border-b">
                    <Avatar class="h-20 w-20 ring-4 ring-background shadow-lg">
                        <AvatarImage :src="employee.avatar_url || ''" :alt="employee.full_name" />
                        <AvatarFallback class="text-2xl font-semibold bg-primary text-primary-foreground">{{ getInitials(employee.full_name) }}</AvatarFallback>
                    </Avatar>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h2 class="text-2xl font-bold">{{ employee.full_name }}</h2>
                            <Badge :variant="employee.status ? 'default' : 'secondary'">{{ employee.status ? 'Active' : 'Inactive' }}</Badge>
                            <Badge v-if="employee.is_on_probation" variant="outline" class="border-orange-400 text-orange-500">Probation</Badge>
                        </div>
                        <p class="text-muted-foreground">{{ employee.job_title || 'Employee' }}</p>
                        <p class="text-sm text-muted-foreground font-mono mt-1">{{ employee.employee_code }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button variant="outline" as-child>
                            <Link :href="`/dashboard/employees/${employee.uuid}/qr-badge`">
                                <QrCode class="h-4 w-4 mr-2" /> QR Badge
                            </Link>
                        </Button>
                        <Button as-child>
                            <Link :href="`/dashboard/employees/${employee.uuid}/edit`">
                                <Pencil class="h-4 w-4 mr-2" /> Edit
                            </Link>
                        </Button>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" size="icon">
                                    <MoreVertical class="h-4 w-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <!-- Has account: Show Change Password -->
                                <DropdownMenuItem v-if="employee.has_account" as-child>
                                    <Link :href="`/dashboard/employees/${employee.uuid}/change-password`" class="flex items-center cursor-pointer">
                                        <Key class="h-4 w-4 mr-2" />
                                        Change Password
                                    </Link>
                                </DropdownMenuItem>
                                <!-- No account but has email or phone: Show Create Account -->
                                <DropdownMenuItem v-else-if="employee.email || employee.phone_number" as-child>
                                    <Link :href="`/dashboard/employees/${employee.uuid}/create-account`" class="flex items-center cursor-pointer">
                                        <UserPlus class="h-4 w-4 mr-2" />
                                        Create Account
                                    </Link>
                                </DropdownMenuItem>
                                <!-- No account and no email/phone: Show disabled Create Account -->
                                <DropdownMenuItem v-else disabled class="opacity-50">
                                    <UserPlus class="h-4 w-4 mr-2" />
                                    Create Account (No contact info)
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem as-child>
                                    <Link :href="`/dashboard/employees/${employee.uuid}/delete`" class="flex items-center cursor-pointer text-destructive">
                                        <XCircle class="h-4 w-4 mr-2" />
                                        Delete
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 divide-y md:divide-y-0 md:divide-x">
                    <!-- Contact -->
                    <div class="p-6 space-y-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Contact</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-muted">
                                    <Mail class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Email</p>
                                    <p class="text-sm">{{ employee.email || '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-muted">
                                    <Phone class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Phone</p>
                                    <p class="text-sm">{{ employee.phone_number || '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-muted shrink-0">
                                    <MapPin class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Address</p>
                                    <p class="text-sm">{{ employee.current_address || '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employment -->
                    <div class="p-6 space-y-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Employment</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-muted">
                                    <Building2 class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Organization</p>
                                    <p class="text-sm">{{ employee.school_name || '-' }}</p>
                                    <p v-if="employee.department_name" class="text-xs text-muted-foreground">{{ employee.department_name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-muted">
                                    <Banknote class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Salary</p>
                                    <p class="text-sm font-semibold">{{ formatCurrency(employee.salary) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-muted">
                                    <Calendar class="h-4 w-4 text-muted-foreground" />
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Hire Date</p>
                                    <p class="text-sm">{{ formatDate(employee.hire_date) }}</p>
                                </div>
                            </div>
                            <div v-if="employee.employee_type_label">
                                <Badge variant="outline">{{ employee.employee_type_label }}</Badge>
                            </div>
                        </div>
                    </div>

                    <!-- Personal & Certification -->
                    <div class="p-6 space-y-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Details</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div>
                                    <p class="text-xs text-muted-foreground">Gender</p>
                                    <p>{{ employee.gender ? employee.gender.charAt(0).toUpperCase() + employee.gender.slice(1) : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Birth Date</p>
                                    <p>{{ formatDate(employee.date_of_birth) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted-foreground">Birth Place</p>
                                    <p>{{ employee.birth_place || '-' }}</p>
                                </div>
                            </div>

                            <div v-if="employee.certificate || employee.certificate_code" class="pt-3 border-t">
                                <div class="flex items-center gap-2 mb-2">
                                    <Award class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Certification</span>
                                </div>
                                <p class="text-sm">{{ employee.certificate || '-' }}</p>
                                <p v-if="employee.certificate_code" class="text-xs text-muted-foreground font-mono">{{ employee.certificate_code }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Probation Section -->
                <div v-if="employee.probation_date || employee.probation_end_date" class="p-6 border-t bg-muted/30">
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2">
                            <Calendar class="h-4 w-4 text-muted-foreground" />
                            <span class="text-sm font-medium">Probation:</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <span class="text-muted-foreground">{{ formatDate(employee.probation_date) }}</span>
                            <span class="text-muted-foreground">→</span>
                            <span class="text-muted-foreground">{{ formatDate(employee.probation_end_date) }}</span>
                            <Badge :variant="employee.is_on_probation ? 'outline' : 'default'" :class="employee.is_on_probation ? 'border-orange-400 text-orange-500' : ''">
                                {{ employee.is_on_probation ? 'In Progress' : 'Completed' }}
                            </Badge>
                        </div>
                    </div>
                </div>

                <!-- Certificate Image -->
                <div v-if="employee.certificate_image" class="p-6 border-t">
                    <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-3">Certificate Document</h3>
                    <a :href="employee.certificate_image" target="_blank" class="inline-block">
                        <img
                            :src="employee.certificate_image"
                            :alt="`${employee.full_name} Certificate`"
                            class="max-h-48 rounded-lg border shadow-sm object-contain hover:shadow-md transition-shadow"
                        />
                    </a>
                </div>
            </div>

            <!-- Account Status Component -->
            <Account
                :employee-uuid="employee.uuid"
                :employee-name="employee.full_name"
                :employee-email="employee.email"
                :employee-phone="employee.phone_number"
                :has-account="employee.has_account"
                :user="employee.user"
            />

            <!-- Attendance Statistics -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Attendance Statistics</h2>
                    <Button variant="outline" size="sm" as-child>
                        <Link href="/dashboard/attendances/analytics">
                            <TrendingUp class="h-4 w-4 mr-2" /> View Analytics
                        </Link>
                    </Button>
                </div>

                <!-- This Month Stats -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                                    <Calendar class="h-5 w-5 text-primary" />
                                </div>
                                <div>
                                    <p class="text-2xl font-bold">{{ attendanceStats.this_month.total }}</p>
                                    <p class="text-xs text-muted-foreground">Total Days</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-500/10">
                                    <CheckCircle class="h-5 w-5 text-green-500" />
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-green-600">{{ attendanceStats.this_month.present }}</p>
                                    <p class="text-xs text-muted-foreground">Present</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-500/10">
                                    <AlertTriangle class="h-5 w-5 text-yellow-500" />
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-yellow-600">{{ attendanceStats.this_month.late }}</p>
                                    <p class="text-xs text-muted-foreground">Late</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-500/10">
                                    <XCircle class="h-5 w-5 text-red-500" />
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-red-600">{{ attendanceStats.this_month.absent }}</p>
                                    <p class="text-xs text-muted-foreground">Absent</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    <Card>
                        <CardContent class="pt-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-500/10">
                                    <Clock class="h-5 w-5 text-blue-500" />
                                </div>
                                <div>
                                    <p class="text-2xl font-bold">{{ attendanceStats.this_month.work_hours_formatted }}</p>
                                    <p class="text-xs text-muted-foreground">Work Hours</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Summary Cards -->
                <div class="grid md:grid-cols-2 gap-4">
                    <!-- Year & All Time Summary -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Summary</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between py-2 border-b">
                                    <span class="text-sm text-muted-foreground">This Year Total</span>
                                    <span class="font-semibold">{{ attendanceStats.this_year.total }} days</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b">
                                    <span class="text-sm text-muted-foreground">This Year Work Hours</span>
                                    <span class="font-semibold">{{ attendanceStats.this_year.work_hours_formatted }}</span>
                                </div>
                                <div class="flex items-center justify-between py-2 border-b">
                                    <span class="text-sm text-muted-foreground">All Time Total</span>
                                    <span class="font-semibold">{{ attendanceStats.all_time.total }} days</span>
                                </div>
                                <div class="flex items-center justify-between py-2">
                                    <span class="text-sm text-muted-foreground">All Time Work Hours</span>
                                    <span class="font-semibold">{{ attendanceStats.all_time.work_hours_formatted }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Recent Attendance -->
                    <Card>
                        <CardHeader class="pb-3">
                            <CardTitle class="text-base">Recent Attendance</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-2">
                                <div
                                    v-for="record in attendanceStats.recent"
                                    :key="record.uuid"
                                    class="flex items-center justify-between py-2 border-b last:border-0"
                                >
                                    <Link
                                        :href="`/dashboard/attendances/${record.uuid}`"
                                        class="flex items-center gap-3 hover:text-primary transition-colors"
                                    >
                                        <span class="text-sm">{{ record.date }}</span>
                                        <Badge :variant="getStatusVariant(record.status)" class="text-xs">
                                            {{ record.status_label }}
                                        </Badge>
                                    </Link>
                                    <div class="text-sm text-muted-foreground">
                                        <span v-if="record.check_in">{{ record.check_in }}</span>
                                        <span v-if="record.check_in && record.check_out"> - {{ record.check_out }}</span>
                                    </div>
                                </div>
                                <div v-if="attendanceStats.recent.length === 0" class="text-center py-4 text-muted-foreground text-sm">
                                    No attendance records yet
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
