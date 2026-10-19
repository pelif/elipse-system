<template>
    <div class="p-6" :class="{ 'max-h-[70vh] overflow-y-auto': inModal }">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes do cliente</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize os dados cadastrais.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Nome fantasia</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ customer?.companyName ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Razão social</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ customer?.legalName ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">CNPJ</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ formatCnpj(customer?.document) }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Código</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ customer?.codeCustomer ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Ativo</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">
                    {{ customer?.active ? 'Sim' : 'Não' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Comissionado</div>
                <div class="mt-1 text-sm font-semibold text-gray-900">
                    {{ comissionedAgentLabel }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Atualização</div>
                <div class="mt-1 text-sm text-gray-700">{{ formatDate(customer?.updatedAt) }}</div>
            </div>

            <div
                v-if="customer?.contacts && customer.contacts.length > 0"
                class="rounded-xl border border-gray-100 bg-gray-50 p-4"
            >
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold mb-2">Contatos</div>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li v-for="c in customer.contacts" :key="c.id ?? c.email">
                        {{ c.name ?? '-' }} · {{ c.phone ?? '-' }} · {{ c.email ?? '-' }}
                        <span v-if="c.typeContact" class="text-gray-500">({{ c.typeContact }})</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    customer: {
        type: Object,
        default: () => ({}),
    },
    inModal: {
        type: Boolean,
        default: false,
    },
});

const comissionedAgentLabel = computed(() => {
    const agent = props.customer?.comissionedAgent;
    if (!agent) {
        return '-';
    }

    const name = agent.name ?? '';
    if (name === '') {
        return '-';
    }

    return name;
});

const formatDate = (iso) => {
    if (!iso) return '-';
    try {
        const d = new Date(iso);
        return d.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
    } catch {
        return '-';
    }
};

const formatCnpj = (raw) => {
    if (!raw) return '-';
    const d = String(raw).replace(/\D/g, '').slice(0, 14);
    if (d.length !== 14) return String(raw);
    return d.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
};
</script>
