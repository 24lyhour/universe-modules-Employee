<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Progress } from '@/components/ui/progress';
import { ScrollArea } from '@/components/ui/scroll-area';
import {
    ArrowLeft,
    Upload,
    Download,
    FileSpreadsheet,
    AlertCircle,
    CheckCircle,
    XCircle,
    AlertTriangle,
    RefreshCcw,
    ChevronRight,
    ChevronLeft,
    Eye,
    Loader2,
} from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';

interface DuplicateOption {
    value: string;
    label: string;
    description: string;
}

interface PreviewRow {
    row_number: number;
    first_name: string | null;
    last_name: string | null;
    email: string | null;
    phone_number: string | null;
    gender: string | null;
    school: string | null;
    department: string | null;
    job_title: string | null;
    employee_type: string | null;
    status: 'ready' | 'error' | 'skip' | 'update';
    errors: string[];
    warnings: string[];
    is_duplicate: boolean;
    existing_employee: { id: number; name: string; employee_code: string } | null;
}

interface PreviewStats {
    total: number;
    ready: number;
    update: number;
    skip: number;
    error: number;
}

const props = defineProps<{
    duplicateOptions: DuplicateOption[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Employees', href: '/dashboard/employees' },
    { title: 'Import', href: '/dashboard/employees/import' },
];

// State
const currentStep = ref<'upload' | 'preview' | 'importing' | 'complete'>('upload');
const selectedFile = ref<File | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const dragActive = ref(false);
const duplicateHandling = ref<string>('skip');
const errors = ref<{ file?: string }>({});

// Preview state
const previewLoading = ref(false);
const previewData = ref<PreviewRow[]>([]);
const previewStats = ref<PreviewStats | null>(null);
const previewError = ref<string | null>(null);

// Import state
const importProgress = ref(0);
const importProcessing = ref(false);

// Computed
const canProceedToPreview = computed(() => selectedFile.value !== null);
const canImport = computed(() => previewStats.value && previewStats.value.ready + previewStats.value.update > 0);

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        selectedFile.value = target.files[0];
        errors.value = {};
        previewData.value = [];
        previewStats.value = null;
        previewError.value = null;
    }
};

const handleDrop = (event: DragEvent) => {
    event.preventDefault();
    dragActive.value = false;
    if (event.dataTransfer?.files && event.dataTransfer.files[0]) {
        const file = event.dataTransfer.files[0];
        if (isValidFile(file)) {
            selectedFile.value = file;
            errors.value = {};
            previewData.value = [];
            previewStats.value = null;
            previewError.value = null;
        }
    }
};

const handleDragOver = (event: DragEvent) => {
    event.preventDefault();
    dragActive.value = true;
};

const handleDragLeave = () => {
    dragActive.value = false;
};

const isValidFile = (file: File): boolean => {
    const validTypes = [
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'text/csv',
    ];
    return validTypes.includes(file.type) || file.name.endsWith('.xlsx') || file.name.endsWith('.xls') || file.name.endsWith('.csv');
};

const handlePreview = async () => {
    if (!selectedFile.value) return;

    previewLoading.value = true;
    previewError.value = null;

    const formData = new FormData();
    formData.append('file', selectedFile.value);
    formData.append('duplicate_handling', duplicateHandling.value);

    try {
        const response = await fetch('/dashboard/employees/import/preview', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            previewData.value = data.preview;
            previewStats.value = data.stats;
            currentStep.value = 'preview';
        } else {
            previewError.value = data.message || 'Preview failed';
        }
    } catch (e) {
        previewError.value = 'Failed to preview file. Please try again.';
    } finally {
        previewLoading.value = false;
    }
};

const handleImport = () => {
    if (!selectedFile.value) return;

    importProcessing.value = true;
    currentStep.value = 'importing';
    importProgress.value = 0;

    // Simulate progress
    const progressInterval = setInterval(() => {
        if (importProgress.value < 90) {
            importProgress.value += 10;
        }
    }, 200);

    router.post('/dashboard/employees/import', {
        file: selectedFile.value,
        duplicate_handling: duplicateHandling.value,
    }, {
        forceFormData: true,
        onSuccess: () => {
            clearInterval(progressInterval);
            importProgress.value = 100;
            currentStep.value = 'complete';
        },
        onError: (errs) => {
            clearInterval(progressInterval);
            errors.value = errs as { file?: string };
            currentStep.value = 'preview';
        },
        onFinish: () => {
            importProcessing.value = false;
        },
    });
};

const handleBack = () => {
    router.visit('/dashboard/employees');
};

const handleBackToUpload = () => {
    currentStep.value = 'upload';
    previewData.value = [];
    previewStats.value = null;
};

const handleDownloadTemplate = () => {
    window.location.href = '/dashboard/employees/template';
};

const removeFile = () => {
    selectedFile.value = null;
    errors.value = {};
    previewData.value = [];
    previewStats.value = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getStatusBadge = (status: string) => {
    switch (status) {
        case 'ready':
            return { variant: 'default' as const, label: 'Ready', icon: CheckCircle };
        case 'update':
            return { variant: 'secondary' as const, label: 'Update', icon: RefreshCcw };
        case 'skip':
            return { variant: 'outline' as const, label: 'Skip', icon: AlertTriangle };
        case 'error':
            return { variant: 'destructive' as const, label: 'Error', icon: XCircle };
        default:
            return { variant: 'default' as const, label: status, icon: CheckCircle };
    }
};

const handleDuplicateChange = (value: string | number | boolean | Record<string, unknown> | null | undefined) => {
    if (typeof value === 'string') {
        duplicateHandling.value = value;
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Import Employees" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center gap-4">
                <Button variant="ghost" size="icon" @click="handleBack">
                    <ArrowLeft class="h-4 w-4" />
                </Button>
                <div>
                    <h1 class="text-2xl font-bold">Import Employees</h1>
                    <p class="text-sm text-muted-foreground">
                        Upload an Excel or CSV file to import employees
                    </p>
                </div>
            </div>

            <!-- Step Indicator -->
            <div class="flex items-center justify-center gap-2">
                <div :class="['flex items-center gap-2 px-4 py-2 rounded-full text-sm', currentStep === 'upload' ? 'bg-primary text-primary-foreground' : 'bg-muted']">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-background text-foreground text-xs font-medium">1</span>
                    <span class="hidden sm:inline">Upload</span>
                </div>
                <ChevronRight class="h-4 w-4 text-muted-foreground" />
                <div :class="['flex items-center gap-2 px-4 py-2 rounded-full text-sm', currentStep === 'preview' ? 'bg-primary text-primary-foreground' : 'bg-muted']">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-background text-foreground text-xs font-medium">2</span>
                    <span class="hidden sm:inline">Preview</span>
                </div>
                <ChevronRight class="h-4 w-4 text-muted-foreground" />
                <div :class="['flex items-center gap-2 px-4 py-2 rounded-full text-sm', currentStep === 'importing' || currentStep === 'complete' ? 'bg-primary text-primary-foreground' : 'bg-muted']">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-background text-foreground text-xs font-medium">3</span>
                    <span class="hidden sm:inline">Import</span>
                </div>
            </div>

            <!-- Step: Upload -->
            <div v-if="currentStep === 'upload'" class="grid gap-6 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Upload File</CardTitle>
                        <CardDescription>
                            Select an Excel (.xlsx, .xls) or CSV file to import employees
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Drop Zone -->
                        <div
                            :class="[
                                'border-2 border-dashed rounded-lg p-8 text-center transition-colors cursor-pointer',
                                dragActive ? 'border-primary bg-primary/5' : 'border-muted-foreground/25 hover:border-primary/50',
                                selectedFile ? 'bg-muted/50' : '',
                            ]"
                            @click="fileInput?.click()"
                            @drop="handleDrop"
                            @dragover="handleDragOver"
                            @dragleave="handleDragLeave"
                        >
                            <input
                                ref="fileInput"
                                type="file"
                                class="hidden"
                                accept=".xlsx,.xls,.csv"
                                @change="handleFileSelect"
                            />
                            <div v-if="!selectedFile" class="space-y-2">
                                <Upload class="mx-auto h-10 w-10 text-muted-foreground" />
                                <p class="text-sm text-muted-foreground">
                                    Drag and drop your file here, or click to browse
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Supported formats: .xlsx, .xls, .csv (max 10MB)
                                </p>
                            </div>
                            <div v-else class="space-y-2">
                                <FileSpreadsheet class="mx-auto h-10 w-10 text-green-600" />
                                <p class="font-medium">{{ selectedFile.name }}</p>
                                <p class="text-sm text-muted-foreground">
                                    {{ formatFileSize(selectedFile.size) }}
                                </p>
                                <Button variant="outline" size="sm" @click.stop="removeFile">
                                    Remove File
                                </Button>
                            </div>
                        </div>

                        <!-- Duplicate Handling -->
                        <div class="space-y-2">
                            <Label>Duplicate Handling</Label>
                            <Select :model-value="duplicateHandling" @update:model-value="handleDuplicateChange">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select duplicate handling" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="option in duplicateOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        <div class="flex flex-col">
                                            <span>{{ option.label }}</span>
                                            <span class="text-xs text-muted-foreground">{{ option.description }}</span>
                                        </div>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <!-- Error -->
                        <Alert v-if="errors.file || previewError" variant="destructive">
                            <AlertCircle class="h-4 w-4" />
                            <AlertTitle>Error</AlertTitle>
                            <AlertDescription>{{ errors.file || previewError }}</AlertDescription>
                        </Alert>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <Button
                                :disabled="!canProceedToPreview || previewLoading"
                                @click="handlePreview"
                            >
                                <Loader2 v-if="previewLoading" class="mr-2 h-4 w-4 animate-spin" />
                                <Eye v-else class="mr-2 h-4 w-4" />
                                {{ previewLoading ? 'Analyzing...' : 'Preview Import' }}
                            </Button>
                            <Button variant="outline" @click="handleBack">
                                Cancel
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Instructions Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>Import Instructions</CardTitle>
                        <CardDescription>
                            Follow these steps for a successful import
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <ol class="list-decimal list-inside space-y-2 text-sm">
                            <li>Download the import template using the button below</li>
                            <li>Fill in the employee data following the template format</li>
                            <li>Save the file as .xlsx, .xls, or .csv</li>
                            <li>Upload the file and preview before importing</li>
                        </ol>

                        <Alert>
                            <AlertCircle class="h-4 w-4" />
                            <AlertTitle>Required Fields</AlertTitle>
                            <AlertDescription>
                                First Name and Last Name are required. Email must be unique if provided.
                            </AlertDescription>
                        </Alert>

                        <div class="space-y-2">
                            <Label class="text-sm font-medium">Available Columns</Label>
                            <div class="grid grid-cols-2 gap-1 text-xs text-muted-foreground">
                                <span>First Name *</span>
                                <span>Last Name *</span>
                                <span>Email</span>
                                <span>Phone Number</span>
                                <span>Gender</span>
                                <span>Date of Birth</span>
                                <span>Birth Place</span>
                                <span>Current Address</span>
                                <span>School</span>
                                <span>Department</span>
                                <span>Employee Type Name</span>
                                <span>Job Title</span>
                                <span>Employment Type</span>
                                <span>Salary</span>
                                <span>Hire Date</span>
                                <span>Probation Date</span>
                                <span>Probation End Date</span>
                                <span>Status</span>
                            </div>
                        </div>

                        <Button variant="outline" class="w-full" @click="handleDownloadTemplate">
                            <Download class="mr-2 h-4 w-4" />
                            Download Template
                        </Button>
                    </CardContent>
                </Card>
            </div>

            <!-- Step: Preview -->
            <Card v-if="currentStep === 'preview'">
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>Preview Import Data</CardTitle>
                            <CardDescription>
                                Review the data before importing. Fix any errors and try again if needed.
                            </CardDescription>
                        </div>
                        <div v-if="previewStats" class="flex gap-2">
                            <Badge variant="default">{{ previewStats.ready }} Ready</Badge>
                            <Badge v-if="previewStats.update > 0" variant="secondary">{{ previewStats.update }} Update</Badge>
                            <Badge v-if="previewStats.skip > 0" variant="outline">{{ previewStats.skip }} Skip</Badge>
                            <Badge v-if="previewStats.error > 0" variant="destructive">{{ previewStats.error }} Errors</Badge>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <ScrollArea class="h-[400px] rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead class="w-16">Row</TableHead>
                                    <TableHead class="w-24">Status</TableHead>
                                    <TableHead>First Name</TableHead>
                                    <TableHead>Last Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Phone</TableHead>
                                    <TableHead>Gender</TableHead>
                                    <TableHead>Job Title</TableHead>
                                    <TableHead>Issues</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="row in previewData"
                                    :key="row.row_number"
                                    :class="{ 'bg-destructive/10': row.status === 'error' }"
                                >
                                    <TableCell class="font-medium">{{ row.row_number }}</TableCell>
                                    <TableCell>
                                        <Badge :variant="getStatusBadge(row.status).variant" class="gap-1">
                                            <component :is="getStatusBadge(row.status).icon" class="h-3 w-3" />
                                            {{ getStatusBadge(row.status).label }}
                                        </Badge>
                                    </TableCell>
                                    <TableCell>{{ row.first_name || '-' }}</TableCell>
                                    <TableCell>{{ row.last_name || '-' }}</TableCell>
                                    <TableCell>
                                        <div class="flex items-center gap-1">
                                            {{ row.email || '-' }}
                                            <Badge v-if="row.is_duplicate" variant="outline" class="text-xs">Duplicate</Badge>
                                        </div>
                                    </TableCell>
                                    <TableCell>{{ row.phone_number || '-' }}</TableCell>
                                    <TableCell>{{ row.gender || '-' }}</TableCell>
                                    <TableCell>{{ row.job_title || '-' }}</TableCell>
                                    <TableCell class="max-w-xs">
                                        <div v-if="row.errors.length" class="text-xs text-destructive">
                                            {{ row.errors.join(', ') }}
                                        </div>
                                        <div v-if="row.warnings.length" class="text-xs text-orange-600">
                                            {{ row.warnings.join(', ') }}
                                        </div>
                                        <span v-if="!row.errors.length && !row.warnings.length" class="text-muted-foreground">-</span>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </ScrollArea>
                </CardContent>
                <CardFooter class="flex justify-between">
                    <Button variant="outline" @click="handleBackToUpload">
                        <ChevronLeft class="mr-2 h-4 w-4" />
                        Back to Upload
                    </Button>
                    <div class="flex gap-2">
                        <Button variant="outline" @click="handleBack">Cancel</Button>
                        <Button :disabled="!canImport" @click="handleImport">
                            <Upload class="mr-2 h-4 w-4" />
                            Import {{ previewStats ? previewStats.ready + previewStats.update : 0 }} Employees
                        </Button>
                    </div>
                </CardFooter>
            </Card>

            <!-- Step: Importing -->
            <Card v-if="currentStep === 'importing'" class="max-w-md mx-auto">
                <CardHeader class="text-center">
                    <Loader2 class="mx-auto h-12 w-12 animate-spin text-primary" />
                    <CardTitle>Importing Employees</CardTitle>
                    <CardDescription>
                        Please wait while we import your data...
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Progress :model-value="importProgress" class="w-full" />
                    <p class="text-center text-sm text-muted-foreground mt-2">{{ importProgress }}%</p>
                </CardContent>
            </Card>

            <!-- Step: Complete (handled by redirect, but shown briefly) -->
            <Card v-if="currentStep === 'complete'" class="max-w-md mx-auto">
                <CardHeader class="text-center">
                    <CheckCircle class="mx-auto h-12 w-12 text-green-600" />
                    <CardTitle>Import Complete</CardTitle>
                    <CardDescription>
                        Redirecting to employee list...
                    </CardDescription>
                </CardHeader>
            </Card>
        </div>
    </AppLayout>
</template>
