<script setup lang="ts">
import { useTranslation } from '@/composables/useTranslation';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
import { Award, FileText, Hash } from 'lucide-vue-next';
import type { InertiaForm } from '@inertiajs/vue3';
import type { EmployeeFormData } from '../../../../types';

interface Props {
    form: InertiaForm<EmployeeFormData>;
}

defineProps<Props>();

const { __ } = useTranslation();
</script>

<template>
    <Card>
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-base">
                <Award class="h-4 w-4 text-primary" />
                {{ __('Certification') }}
            </CardTitle>
            <CardDescription>{{ __('Educational qualifications and credentials') }}</CardDescription>
        </CardHeader>
        <CardContent>
            <div class="grid gap-4 sm:grid-cols-2 rounded-lg border bg-muted/30 p-4">
                <div class="space-y-2">
                    <Label for="certificate" class="text-xs font-medium">{{ __('Certificate Name') }}</Label>
                    <div class="relative">
                        <FileText class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input id="certificate" v-model="form.certificate" type="text" :placeholder="__('Bachelor\'s Degree')" class="pl-10 bg-background" />
                    </div>
                    <p v-if="form.errors.certificate" class="text-xs text-destructive">
                        {{ form.errors.certificate }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="certificate_code" class="text-xs font-medium">{{ __('Certificate Code') }}</Label>
                    <div class="relative">
                        <Hash class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                        <Input id="certificate_code" v-model="form.certificate_code" type="text" placeholder="CERT-2024-001" class="pl-10 bg-background" />
                    </div>
                    <p v-if="form.errors.certificate_code" class="text-xs text-destructive">
                        {{ form.errors.certificate_code }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
