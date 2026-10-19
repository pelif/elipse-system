<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes do Status de Job</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações do status selecionado.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Status</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ status?.status ?? '-' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Observações</div>
                <div class="mt-1 text-sm text-gray-700 whitespace-pre-wrap wrap-break-word">
                    {{ status?.observacoes || '-' }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    status: {
        type: Object,
        default: () => ({}),
    },
});
</script>

