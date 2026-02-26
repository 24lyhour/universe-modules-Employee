<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { TrashTable, ButtonGroup } from '@/components/shared';
import { Button } from '@/components/ui/button';
import { Database, Trash2 } from 'lucide-vue-next';
import type { BreadcrumbItem } from '@/types';
import type { TrashPaginationData, TrashConfigLocal, TrashConfig } from '@/types/trash';

interface Props {
    trashItems: TrashPaginationData;
    config: TrashConfig;
    filters: {
        search?: string;
        per_page?: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Employees', href: '/dashboard/employees' },
    { title: 'Trash', href: '/dashboard/employees/trash' },
];

const trashConfig: TrashConfigLocal = {
    entityLabel: 'Employee',
    entityLabelPlural: 'Employees',
    restoreRoute: (uuid: string) => `/dashboard/employees/${uuid}/restore`,
    forceDeleteRoute: (uuid: string) => `/dashboard/employees/${uuid}/force-delete`,
    listRoute: '/dashboard/employees/trash',
};

const handleAll = () => {
    router.visit('/dashboard/employees');
};

const handlePageChange = (page: number) => {
    router.get('/dashboard/employees/trash', {
        page,
        per_page: props.trashItems.meta?.per_page || 10,
        search: props.filters.search,
    }, { preserveState: true });
};

const handleSearch = (query: string) => {
    router.get('/dashboard/employees/trash', {
        search: query || undefined,
        per_page: props.trashItems.meta?.per_page || 10,
    }, { preserveState: true });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Employees Trash" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Employees Trash</h1>
                    <p class="text-sm text-muted-foreground">
                        Manage deleted employees - restore or permanently delete
                    </p>
                </div>
                <ButtonGroup>
                    <Button variant="outline" @click="handleAll">
                        <Database class="mr-2 h-4 w-4" />
                        All
                    </Button>
                    <Button variant="default">
                        <Trash2 class="mr-2 h-4 w-4" />
                        Trash
                    </Button>
                </ButtonGroup>
            </div>

            <!-- Trash Table -->
            <TrashTable
                :items="trashItems.data"
                :config="trashConfig"
                :pagination="trashItems.meta"
                :show-type="false"
                empty-message="No deleted employees found."
                empty-trash-route="/dashboard/employees/trash/empty"
                @page-change="handlePageChange"
                @search="handleSearch"
            />
        </div>
    </AppLayout>
</template>
