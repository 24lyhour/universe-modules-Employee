<script setup lang="ts">
import { computed } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Trash2 } from 'lucide-vue-next';
import type { FamilyMemberFormData, Gender } from '../../../../types';

interface Props {
    member: FamilyMemberFormData;
    title: string;
    index?: number;
    showGender?: boolean;
    showEmail?: boolean;
    showDependent?: boolean;
    genderOptions?: { value: string; label: string }[];
}

const props = withDefaults(defineProps<Props>(), {
    showGender: false,
    showEmail: false,
    showDependent: false,
    genderOptions: () => [
        { value: 'male', label: 'Male' },
        { value: 'female', label: 'Female' },
        { value: 'other', label: 'Other' },
    ],
});

const emit = defineEmits<{
    remove: [];
}>();

const memberGender = computed({
    get: () => props.member.gender || undefined,
    set: (value: string | undefined) => {
        props.member.gender = (value as Gender) || null;
    },
});
</script>

<template>
    <div class="rounded-lg border bg-muted/30 p-4">
        <div class="mb-3 flex items-center justify-between">
            <span class="text-sm font-medium text-muted-foreground">
                {{ title }}{{ index !== undefined ? ` ${index + 1}` : '' }}
            </span>
            <Button
                type="button"
                variant="ghost"
                size="icon"
                class="h-8 w-8 text-destructive hover:text-destructive"
                @click="emit('remove')"
            >
                <Trash2 class="h-4 w-4" />
            </Button>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="space-y-2">
                <Label>Name <span class="text-destructive">*</span></Label>
                <Input v-model="member.name" placeholder="Full name" />
            </div>
            <div v-if="showGender" class="space-y-2">
                <Label>Gender</Label>
                <Select v-model="memberGender">
                    <SelectTrigger>
                        <SelectValue placeholder="Select gender" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="opt in genderOptions" :key="opt.value" :value="opt.value">
                            {{ opt.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="space-y-2">
                <Label>Date of Birth</Label>
                <Input v-model="member.date_of_birth" type="date" />
            </div>
            <div class="space-y-2">
                <Label>Occupation</Label>
                <Input v-model="member.occupation" placeholder="Job / Occupation" />
            </div>
            <div class="space-y-2">
                <Label>Phone Number</Label>
                <Input v-model="member.phone_number" placeholder="+1 234 567 8900" />
            </div>
            <div v-if="showEmail" class="space-y-2">
                <Label>Email</Label>
                <Input v-model="member.email" type="email" placeholder="email@example.com" />
            </div>
        </div>
        <div class="mt-4 flex gap-4">
            <div class="flex items-center gap-2">
                <Switch v-model="member.is_emergency_contact" />
                <Label class="text-sm font-normal">Emergency Contact</Label>
            </div>
            <div v-if="showDependent" class="flex items-center gap-2">
                <Switch v-model="member.is_dependent" />
                <Label class="text-sm font-normal">Dependent</Label>
            </div>
        </div>
    </div>
</template>
