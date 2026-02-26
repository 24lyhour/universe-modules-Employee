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
    { title: 'Attendances', href: '/dashboard/attendances' },
    { title: 'Trash', href: '/dashboard/attendances/trash' },
];

const trashConfig: TrashConfigLocal = {
    entityLabel: 'Attendance',
    entityLabelPlural: 'Attendances',
    restoreRoute: (uuid: string) => `/dashboard/attendances/${uuid}/restore`,
    forceDeleteRoute: (uuid: string) => `/dashboard/attendances/${uuid}/force-delete`,
    listRoute: '/dashboard/attendances/trash',
};

const handleAll = () => {
    router.visit('/dashboard/attendances');
};

const handlePageChange = (page: number) => {
    router.get('/dashboard/attendances/trash', {
        page,
        per_page: props.trashItems.meta?.per_page || 10,
        search: props.filters.search,
    }, { preserveState: true });
};

const handleSearch = (query: string) => {
    router.get('/dashboard/attendances/trash', {
        search: query || undefined,
        per_page: props.trashItems.meta?.per_page || 10,
    }, { preserveState: true });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Attendances Trash" />

        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Attendances Trash</h1>
                    <p class="text-sm text-muted-foreground">
                        Manage deleted attendance records - restore or permanently delete
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
                empty-message="No deleted attendance records found."
                empty-trash-route="/dashboard/attendances/trash/empty"
                @page-change="handlePageChange"
                @search="handleSearch"
            />
        </div>
    </AppLayout>
</template>
