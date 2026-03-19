<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Plus } from 'lucide-vue-next';

interface Props {
    title: string;
    addLabel: string;
    showAddButton?: boolean;
    emptyMessage: string;
    isEmpty: boolean;
    hasBorderTop?: boolean;
}

withDefaults(defineProps<Props>(), {
    showAddButton: true,
    hasBorderTop: false,
});

const emit = defineEmits<{
    add: [];
}>();
</script>

<template>
    <div class="space-y-4" :class="{ 'border-t pt-6': hasBorderTop }">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-medium">{{ title }}</h4>
            <Button
                v-if="showAddButton"
                type="button"
                variant="outline"
                size="sm"
                @click="emit('add')"
            >
                <Plus class="mr-1 h-3 w-3" /> {{ addLabel }}
            </Button>
        </div>
        <slot />
        <p v-if="isEmpty" class="text-sm text-muted-foreground">
            {{ emptyMessage }}
        </p>
    </div>
</template>
