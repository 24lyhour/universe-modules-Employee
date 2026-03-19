<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { ImageUpload } from '@/components/shared';
import type { InertiaForm } from '@inertiajs/vue3';
import type { EmployeeFormData } from '../../../../types';

interface Props {
    form: InertiaForm<EmployeeFormData>;
    mode?: 'create' | 'edit';
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
});

// Avatar images
const avatarImages = computed({
    get: () => props.form.avatar_url ? [props.form.avatar_url] : [],
    set: (value: string[]) => {
        props.form.avatar_url = value.length > 0 ? value[0] : '';
    },
});

// Certificate images
const certificateImages = computed({
    get: () => props.form.certificate_image ? [props.form.certificate_image] : [],
    set: (value: string[]) => {
        props.form.certificate_image = value.length > 0 ? value[0] : '';
    },
});

// Status
const isActive = computed({
    get: () => props.form.status,
    set: (value: boolean) => {
        props.form.status = value;
    },
});

// Create account
const createAccount = computed({
    get: () => props.form.create_account ?? false,
    set: (value: boolean) => {
        props.form.create_account = value;
        if (!value) {
            props.form.password = '';
            props.form.password_confirmation = '';
        }
    },
});
</script>

<template>
    <div class="space-y-6">
        <!-- Profile Photo Card -->
        <Card>
            <CardHeader class="pb-3">
                <CardTitle class="text-base">Profile Photo</CardTitle>
            </CardHeader>
            <CardContent>
                <ImageUpload
                    v-model="avatarImages"
                    label=""
                    :multiple="false"
                    :max-files="1"
                    :max-size="5"
                    :error="form.errors.avatar_url"
                />
            </CardContent>
        </Card>

        <!-- Status Card -->
        <Card>
            <CardHeader class="pb-3">
                <CardTitle class="text-base">Status</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Employee Status</p>
                        <p class="text-xs text-muted-foreground">
                            {{ isActive ? 'Employee is active' : 'Employee is inactive' }}
                        </p>
                    </div>
                    <Switch v-model="isActive" />
                </div>
            </CardContent>
        </Card>

        <!-- Account Settings Card (Only for Create mode) -->
        <Card v-if="mode === 'create'">
            <CardHeader class="pb-3">
                <CardTitle class="text-base">Account Settings</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Create Login Account</p>
                        <p class="text-xs text-muted-foreground">Allow employee to log in</p>
                    </div>
                    <Switch v-model="createAccount" />
                </div>

                <div v-if="createAccount" class="space-y-4 border-t pt-4">
                    <div class="space-y-2">
                        <Label for="password">Password <span class="text-destructive">*</span></Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            placeholder="Minimum 8 characters"
                            autocomplete="new-password"
                        />
                        <p v-if="form.errors.password" class="text-xs text-destructive">
                            {{ form.errors.password }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label for="password_confirmation">Confirm Password <span class="text-destructive">*</span></Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            placeholder="Repeat password"
                            autocomplete="new-password"
                        />
                    </div>
                    <p class="text-xs text-muted-foreground">Employee will use their email to log in.</p>
                </div>
            </CardContent>
        </Card>

        <!-- Certificate Document Card -->
        <Card>
            <CardHeader class="pb-3">
                <CardTitle class="text-base">Certificate Document</CardTitle>
            </CardHeader>
            <CardContent>
                <ImageUpload
                    v-model="certificateImages"
                    label=""
                    :multiple="false"
                    :max-files="1"
                    :max-size="5"
                    :error="form.errors.certificate_image"
                />
            </CardContent>
        </Card>
    </div>
</template>
