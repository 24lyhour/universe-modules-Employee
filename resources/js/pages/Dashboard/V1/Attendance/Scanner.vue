<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    QrCode,
    Camera,
    LogIn,
    LogOut,
    CheckCircle,
    XCircle,
    Clock,
    RefreshCw,
    Users,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { AttendanceScannerProps } from '@employee/types';
import axios from 'axios';

const props = defineProps<AttendanceScannerProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Attendance', href: '/dashboard/attendances' },
    { title: 'QR Scanner', href: '/dashboard/attendances/scanner' },
];

// State
const scanType = ref<'check_in' | 'check_out'>('check_in');
const locationType = ref<'department' | 'classroom'>('department');
const locationId = ref<string>('');
const qrCode = ref('');
const isScanning = ref(false);
const isProcessing = ref(false);
const scanResult = ref<{
    success: boolean;
    message: string;
    data?: {
        employee?: {
            id: number;
            full_name: string;
            employee_code: string;
            avatar_url: string | null;
            department: string | null;
            job_title: string | null;
        };
        attendance?: {
            check_in_time: string;
            check_out_time: string | null;
            status: string;
            work_hours?: number;
        };
    };
} | null>(null);

const recentScans = ref<Array<{
    id: number;
    employee_name: string;
    employee_code: string;
    avatar_url: string | null;
    time: string;
    type: 'check_in' | 'check_out';
    status: string;
}>>([]);

const todayStats = ref({
    total_employees: 0,
    checked_in: 0,
    checked_out: 0,
    present: 0,
    late: 0,
    absent: 0,
});

// Computed
const filteredLocationOptions = computed(() => {
    if (locationType.value === 'department') {
        return props.departmentOptions;
    }
    return props.classroomOptions;
});

const getInitials = (name: string | null) => {
    if (!name) return '?';
    return name
        .split(' ')
        .map((n) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

// Methods
const fetchTodaySummary = async () => {
    try {
        const response = await axios.get('/dashboard/attendances/today-summary');
        todayStats.value = response.data.stats;
        recentScans.value = response.data.recent_scans.map((scan: any) => ({
            id: scan.id,
            employee_name: scan.employee.name,
            employee_code: scan.employee.code,
            avatar_url: scan.employee.avatar,
            time: scan.check_out || scan.check_in,
            type: scan.check_out ? 'check_out' : 'check_in',
            status: scan.status,
        }));
    } catch (error) {
        console.error('Failed to fetch today summary:', error);
    }
};

const processScan = async () => {
    if (!qrCode.value.trim()) {
        scanResult.value = {
            success: false,
            message: 'Please enter or scan a QR code.',
        };
        return;
    }

    isProcessing.value = true;
    scanResult.value = null;

    try {
        const response = await axios.post('/dashboard/attendances/scan', {
            qr_code: qrCode.value.trim(),
            scan_type: scanType.value,
            location_type: locationType.value,
            location_id: locationId.value ? parseInt(locationId.value) : null,
        });

        scanResult.value = response.data;

        if (response.data.success) {
            // Add to recent scans
            const employee = response.data.data?.employee;
            const attendance = response.data.data?.attendance;
            if (employee && attendance) {
                recentScans.value.unshift({
                    id: Date.now(),
                    employee_name: employee.full_name,
                    employee_code: employee.employee_code,
                    avatar_url: employee.avatar_url,
                    time: scanType.value === 'check_in'
                        ? attendance.check_in_time
                        : attendance.check_out_time || '',
                    type: scanType.value,
                    status: attendance.status,
                });
                // Keep only last 10
                recentScans.value = recentScans.value.slice(0, 10);
            }

            // Clear QR code after successful scan
            qrCode.value = '';

            // Refresh stats
            fetchTodaySummary();
        }
    } catch (error: any) {
        scanResult.value = {
            success: false,
            message: error.response?.data?.message || 'An error occurred while processing the scan.',
        };
    } finally {
        isProcessing.value = false;
    }
};

const clearResult = () => {
    scanResult.value = null;
    qrCode.value = '';
};

// Lifecycle
let refreshInterval: ReturnType<typeof setInterval>;

onMounted(() => {
    fetchTodaySummary();
    // Auto-refresh every 30 seconds
    refreshInterval = setInterval(fetchTodaySummary, 30000);
});

onUnmounted(() => {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="QR Scanner - Attendance" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Stats Row -->
            <div class="grid gap-4 md:grid-cols-4">
                <Card>
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="rounded-lg bg-blue-500/10 p-3">
                            <Users class="h-6 w-6 text-blue-500" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Total</p>
                            <p class="text-2xl font-bold">{{ todayStats.total_employees }}</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="rounded-lg bg-green-500/10 p-3">
                            <CheckCircle class="h-6 w-6 text-green-500" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Checked In</p>
                            <p class="text-2xl font-bold">{{ todayStats.checked_in }}</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="rounded-lg bg-yellow-500/10 p-3">
                            <Clock class="h-6 w-6 text-yellow-500" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Late</p>
                            <p class="text-2xl font-bold">{{ todayStats.late }}</p>
                        </div>
                    </CardContent>
                </Card>
                <Card>
                    <CardContent class="flex items-center gap-4 p-4">
                        <div class="rounded-lg bg-purple-500/10 p-3">
                            <LogOut class="h-6 w-6 text-purple-500" />
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Checked Out</p>
                            <p class="text-2xl font-bold">{{ todayStats.checked_out }}</p>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <!-- Scanner Card -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <QrCode class="h-5 w-5" />
                            QR Code Scanner
                        </CardTitle>
                        <CardDescription>
                            Scan employee QR code or enter manually
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <!-- Scan Type Toggle -->
                        <div class="flex gap-2">
                            <Button
                                :variant="scanType === 'check_in' ? 'default' : 'outline'"
                                class="flex-1"
                                @click="scanType = 'check_in'"
                            >
                                <LogIn class="mr-2 h-4 w-4" />
                                Check In
                            </Button>
                            <Button
                                :variant="scanType === 'check_out' ? 'default' : 'outline'"
                                class="flex-1"
                                @click="scanType = 'check_out'"
                            >
                                <LogOut class="mr-2 h-4 w-4" />
                                Check Out
                            </Button>
                        </div>

                        <!-- Location Selection -->
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Location Type</Label>
                                <Select v-model="locationType">
                                    <SelectTrigger>
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="department">Department</SelectItem>
                                        <SelectItem value="classroom">Classroom</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Location</Label>
                                <Select v-model="locationId">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select location" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in filteredLocationOptions"
                                            :key="option.value"
                                            :value="option.value.toString()"
                                        >
                                            {{ option.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <!-- QR Code Input -->
                        <div class="space-y-2">
                            <Label>Employee QR Code / ID</Label>
                            <div class="flex gap-2">
                                <Input
                                    v-model="qrCode"
                                    placeholder="Scan or enter QR code..."
                                    class="flex-1"
                                    @keyup.enter="processScan"
                                    autofocus
                                />
                                <Button @click="processScan" :disabled="isProcessing">
                                    <RefreshCw v-if="isProcessing" class="mr-2 h-4 w-4 animate-spin" />
                                    <Camera v-else class="mr-2 h-4 w-4" />
                                    {{ isProcessing ? 'Processing...' : 'Process' }}
                                </Button>
                            </div>
                        </div>

                        <!-- Result Display -->
                        <div v-if="scanResult" class="rounded-lg border p-4">
                            <div
                                :class="[
                                    'flex items-start gap-3',
                                    scanResult.success ? 'text-green-600' : 'text-red-600'
                                ]"
                            >
                                <CheckCircle v-if="scanResult.success" class="h-6 w-6 flex-shrink-0" />
                                <XCircle v-else class="h-6 w-6 flex-shrink-0" />
                                <div class="flex-1">
                                    <p class="font-medium">{{ scanResult.message }}</p>

                                    <!-- Employee Info -->
                                    <div v-if="scanResult.data?.employee" class="mt-4 flex items-center gap-3">
                                        <Avatar class="h-12 w-12">
                                            <AvatarImage
                                                :src="scanResult.data.employee.avatar_url || ''"
                                                :alt="scanResult.data.employee.full_name"
                                            />
                                            <AvatarFallback>
                                                {{ getInitials(scanResult.data.employee.full_name) }}
                                            </AvatarFallback>
                                        </Avatar>
                                        <div>
                                            <p class="font-semibold text-foreground">
                                                {{ scanResult.data.employee.full_name }}
                                            </p>
                                            <p class="text-sm text-muted-foreground">
                                                {{ scanResult.data.employee.employee_code }}
                                                <span v-if="scanResult.data.employee.department">
                                                    - {{ scanResult.data.employee.department }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <Button variant="ghost" size="sm" @click="clearResult">
                                    Clear
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Recent Scans -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <Clock class="h-5 w-5" />
                                Recent Scans
                            </span>
                            <Button variant="ghost" size="sm" @click="fetchTodaySummary">
                                <RefreshCw class="h-4 w-4" />
                            </Button>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-if="recentScans.length === 0" class="py-8 text-center text-muted-foreground">
                            No scans recorded today
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="scan in recentScans"
                                :key="scan.id"
                                class="flex items-center justify-between rounded-lg border p-3"
                            >
                                <div class="flex items-center gap-3">
                                    <Avatar class="h-10 w-10">
                                        <AvatarImage :src="scan.avatar_url || ''" :alt="scan.employee_name" />
                                        <AvatarFallback>{{ getInitials(scan.employee_name) }}</AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <p class="font-medium">{{ scan.employee_name }}</p>
                                        <p class="text-sm text-muted-foreground">{{ scan.employee_code }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <Badge :variant="scan.type === 'check_in' ? 'default' : 'secondary'">
                                        {{ scan.type === 'check_in' ? 'In' : 'Out' }}
                                    </Badge>
                                    <p class="mt-1 text-sm text-muted-foreground">{{ scan.time }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
