<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { ArrowLeft, Pencil, Mail, Phone, MapPin, Building2, Calendar, Award, Banknote } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { EmployeeShowProps } from '@employee/types';

const props = defineProps<EmployeeShowProps>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Employees', href: '/dashboard/employees' },
    { title: props.employee.full_name, href: `/dashboard/employees/${props.employee.id}` },
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
                    <Button as-child>
                        <Link :href="`/dashboard/employees/${employee.id}/edit`">
                            <Pencil class="h-4 w-4 mr-2" /> Edit
                        </Link>
                    </Button>
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
        </div>
    </AppLayout>
</template>
