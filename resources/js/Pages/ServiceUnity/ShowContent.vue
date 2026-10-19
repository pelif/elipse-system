<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">
                    Detalhes da Unidade
                </h4>
                <p class="text-sm text-gray-500 mt-1">
                    Visualize as informações da Unidade de Serviço Selecionada.
                </p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">
                    Unidade de Serviço
                </div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ serviceUnity?.unity ?? '-' }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">
                    Data de Criação
                </div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ formatDate(serviceUnity?.createdAt) }}
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">
                    Última Alteração
                </div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ formatDate(serviceUnity?.updatedAt) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    serviceUnity: {
        type: Object,
        default: () => ({})
    }
});

const formatDate = (value) => {
    if (!value) {
        return '-';
    }
    try {
        const d = new Date(value);
        return d.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
    } catch {
        return value;
    }
};
</script>
