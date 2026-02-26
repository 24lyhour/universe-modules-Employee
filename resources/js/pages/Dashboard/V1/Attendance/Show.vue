<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { ArrowLeft, Pencil, Trash2, Clock, MapPin, Smartphone } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { AttendanceShowProps } from '@employee/types';

const props = defineProps<AttendanceShowProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/dashboard/attendances' },
    { title: props.attendance.attendance_date_formatted, href: `/dashboard/attendances/${props.attendance.uuid}` },
];

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

const handleBack = () => {
    router.visit('/dashboard/attendances');
};

const handleEdit = () => {
    router.visit(`/dashboard/attendances/${props.attendance.uuid}/edit`);
};

const handleDelete = () => {
    if (confirm('Are you sure you want to delete this attendance record?')) {
        router.delete(`/dashboard/attendances/${props.attendance.uuid}`);
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="`Attendance - ${attendance.attendance_date_formatted}`" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button variant="ghost" size="icon" @click="handleBack">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <div class="flex items-center gap-4">
                        <Avatar class="h-14 w-14">
                            <AvatarImage
                                :src="attendance.employee_avatar || ''"
                                :alt="attendance.employee_name || ''"
                            />
                            <AvatarFallback>{{ getInitials(attendance.employee_name) }}</AvatarFallback>
                        </Avatar>
                        <div>
                            <h1 class="text-2xl font-bold">{{ attendance.employee_name }}</h1>
                            <p class="text-sm text-muted-foreground">
                                {{ attendance.employee_code }} - {{ attendance.attendance_date_formatted }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" @click="handleEdit">
                        <Pencil class="mr-2 h-4 w-4" />
                        Edit
                    </Button>
                    <Button variant="destructive" @click="handleDelete">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>

            <!-- Content -->
            <div class="grid gap-6 md:grid-cols-2">
                <!-- Attendance Details -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Clock class="h-5 w-5" />
                            Attendance Details
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Date</span>
                            <span class="font-medium">{{ attendance.attendance_date_formatted }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Status</span>
                            <Badge :variant="getStatusVariant(attendance.status)">
                                {{ attendance.status_label }}
                            </Badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Check In</span>
                            <span class="font-medium">{{ attendance.check_in_time || '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Check Out</span>
                            <span class="font-medium">{{ attendance.check_out_time || '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Work Hours</span>
                            <span class="font-medium">{{ attendance.work_hours_formatted }}</span>
                        </div>
                        <div v-if="attendance.overtime_hours" class="flex justify-between">
                            <span class="text-muted-foreground">Overtime</span>
                            <span class="font-medium">{{ attendance.overtime_hours }}h</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Method & Location -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MapPin class="h-5 w-5" />
                            Location & Method
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Department</span>
                            <span class="font-medium">{{ attendance.department_name || '-' }}</span>
                        </div>
                        <div v-if="attendance.classroom_name" class="flex justify-between">
                            <span class="text-muted-foreground">Classroom</span>
                            <span class="font-medium">{{ attendance.classroom_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Check-in Method</span>
                            <Badge variant="outline">{{ attendance.check_in_method_label }}</Badge>
                        </div>
                        <div v-if="attendance.check_out_method_label" class="flex justify-between">
                            <span class="text-muted-foreground">Check-out Method</span>
                            <Badge variant="outline">{{ attendance.check_out_method_label }}</Badge>
                        </div>
                        <div v-if="attendance.check_in_location" class="flex justify-between">
                            <span class="text-muted-foreground">Check-in Location</span>
                            <span class="font-medium">{{ attendance.check_in_location }}</span>
                        </div>
                        <div v-if="attendance.check_out_location" class="flex justify-between">
                            <span class="text-muted-foreground">Check-out Location</span>
                            <span class="font-medium">{{ attendance.check_out_location }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- Device Info -->
                <Card v-if="attendance.device_info || attendance.ip_address">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Smartphone class="h-5 w-5" />
                            Device Information
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div v-if="attendance.ip_address" class="flex justify-between">
                            <span class="text-muted-foreground">IP Address</span>
                            <span class="font-mono text-sm">{{ attendance.ip_address }}</span>
                        </div>
                        <div v-if="attendance.device_info">
                            <span class="text-muted-foreground">Device</span>
                            <p class="mt-1 text-sm">{{ attendance.device_info }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Notes -->
                <Card v-if="attendance.notes">
                    <CardHeader>
                        <CardTitle>Notes</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-muted-foreground">{{ attendance.notes }}</p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
