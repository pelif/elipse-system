<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes do organizador</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações do organizador selecionado.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Nome</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ organizer?.name ?? '-' }}
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
                    {{ displayPhone(organizer?.phone) }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">E-mail</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ organizer?.email ?? '-' }}
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
    organizer: {
        type: Object,
        default: () => ({}),
    },
});

const customerDisplay = computed(() => {
    const o = props.organizer;
    if (!o) {
        return '-';
    }
    if (o.customerName != null && String(o.customerName).trim() !== '') {
        return String(o.customerName);
    }
    return '-';
});

const communicationDisplay = computed(() => {
    const o = props.organizer;
    if (!o || o.communicationCare == null || String(o.communicationCare).trim() === '') {
        return '-';
    }
    return String(o.communicationCare);
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
