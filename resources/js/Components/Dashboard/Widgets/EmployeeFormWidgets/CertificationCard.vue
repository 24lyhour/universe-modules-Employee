<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { InertiaForm } from '@inertiajs/vue3';
import type { EmployeeFormData } from '../../../../types';

interface Props {
    form: InertiaForm<EmployeeFormData>;
}

defineProps<Props>();
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <CardTitle class="text-base">Certification</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-2">
                    <Label for="certificate">Certificate Name</Label>
                    <Input id="certificate" v-model="form.certificate" type="text" placeholder="Bachelor's Degree" />
                    <p v-if="form.errors.certificate" class="text-xs text-destructive">
                        {{ form.errors.certificate }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label for="certificate_code">Certificate Code</Label>
                    <Input id="certificate_code" v-model="form.certificate_code" type="text" placeholder="CERT-2024-001" />
                    <p v-if="form.errors.certificate_code" class="text-xs text-destructive">
                        {{ form.errors.certificate_code }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
