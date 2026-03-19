<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Trash2 } from 'lucide-vue-next';

interface Props {
    title: string;
    index?: number;
}

defineProps<Props>();

const emit = defineEmits<{
    remove: [];
}>();
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
        <slot />
    </div>
</template>
