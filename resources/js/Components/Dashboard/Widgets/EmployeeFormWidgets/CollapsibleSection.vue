<script setup lang="ts">
import { ref } from 'vue';
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { ChevronDown, Plus } from 'lucide-vue-next';
import type { Component } from 'vue';

interface Props {
    title: string;
    icon?: Component;
    showAdd?: boolean;
    addLabel?: string;
    defaultOpen?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showAdd: false,
    addLabel: 'Add',
    defaultOpen: true,
});

const emit = defineEmits<{
    add: [];
}>();

const isOpen = ref(props.defaultOpen);

const handleAdd = () => {
    isOpen.value = true;
    emit('add');
};

defineExpose({ isOpen });
</script>

<template>
    <Collapsible v-model:open="isOpen">
        <Card>
            <CardHeader class="pb-3">
                <div class="flex items-center justify-between">
                    <CardTitle class="flex items-center gap-2 text-base">
                        <component :is="icon" v-if="icon" class="h-4 w-4" />
                        {{ title }}
                    </CardTitle>
                    <div class="flex items-center gap-2">
                        <Button
                            v-if="showAdd"
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="handleAdd"
                        >
                            <Plus class="mr-1 h-3 w-3" /> {{ addLabel }}
                        </Button>
                        <CollapsibleTrigger as-child>
                            <Button variant="ghost" size="sm">
                                <ChevronDown
                                    class="h-4 w-4 transition-transform"
                                    :class="{ 'rotate-180': isOpen }"
                                />
                            </Button>
                        </CollapsibleTrigger>
                    </div>
                </div>
            </CardHeader>
            <CollapsibleContent>
                <CardContent>
                    <slot />
                </CardContent>
            </CollapsibleContent>
        </Card>
    </Collapsible>
</template>
