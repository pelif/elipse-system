<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes do contratante</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações do contratante selecionado.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Nome</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ contractor?.name ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Cliente</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ customerDisplay }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Telefone</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ displayPhone(contractor?.phone) }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">E-mail</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ contractor?.email ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Cuidados de comunicação</div>
                <div class="mt-1 text-sm text-gray-700 whitespace-pre-wrap wrap-break-word">
                    {{ communicationDisplay }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatCellphone } from '@/helpers/masks';

const props = defineProps({
    contractor: {
        type: Object,
        default: () => ({}),
    },
});

const customerDisplay = computed(() => {
    const c = props.contractor;
    if (!c) {
        return '-';
    }
    if (c.customerName != null && String(c.customerName).trim() !== '') {
        return String(c.customerName);
    }
    return '-';
});

const communicationDisplay = computed(() => {
    const c = props.contractor;
    if (!c || c.communicationCare == null || String(c.communicationCare).trim() === '') {
        return '-';
    }
    return String(c.communicationCare);
});

const displayPhone = (raw) => {
    if (!raw) {
        return '-';
    }
    const digits = String(raw).replace(/\D/g, '');
    if (digits === '') {
        return '-';
    }
    return formatCellphone(digits);
};
</script>
