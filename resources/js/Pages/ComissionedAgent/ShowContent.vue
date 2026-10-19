<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes do comissionado</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações do comissionado selecionado.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Nome</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ comissionedAgent?.name ?? '-' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Comissão Padrão (%)</div>
                <div class="mt-1 text-base font-semibold text-gray-900">
                    {{ comissionedAgent?.defaultComission ?? '-' }}%
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Dias de Pagamento</div>
                <div class="mt-1 text-base font-semibold text-gray-900">
                    {{ comissionedAgent?.paymentDays ?? '-' }}
                </div>
            </div>

            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Observações</div>
                <div class="mt-1 text-sm text-gray-700 whitespace-pre-wrap wrap-break-word">
                    {{ comissionedAgent?.observations || '-' }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    comissionedAgent: {
        type: Object,
        default: () => ({}),
    },
});
</script>
