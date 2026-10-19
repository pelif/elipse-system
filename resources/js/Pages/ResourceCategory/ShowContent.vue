<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes da categoria</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações da categoria selecionada.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Nome da categoria</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ resourceCategory?.name ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Descrição</div>
                <div class="mt-1 text-sm text-gray-700 whitespace-pre-wrap wrap-break-word">
                    {{ descriptionDisplay }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    resourceCategory: {
        type: Object,
        default: () => ({}),
    },
});

const descriptionDisplay = computed(() => {
    const c = props.resourceCategory;
    if (!c || c.description == null || String(c.description).trim() === '') {
        return '-';
    }
    return String(c.description);
});
</script>
