<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes do terceiro</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações do terceiro selecionado.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Nome</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ thirdParty?.name ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Apelido</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ thirdParty?.alias || '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Razão social</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ thirdParty?.companyName || '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">CNPJ</div>
                <div class="mt-1 text-sm text-gray-900 font-medium wrap-break-word">
                    {{ displayCnpj(thirdParty?.document) }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Ativo</div>
                <div class="mt-1 text-sm text-gray-700">
                    {{ activeLabel(thirdParty?.active) }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Especialidades</div>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span
                        v-if="!serviceList.length"
                        class="text-sm text-gray-500"
                    >
                        -
                    </span>
                    <span
                        v-for="service in serviceList"
                        :key="service.id"
                        class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-medium"
                    >
                        {{ service.service }}
                    </span>
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Telefone</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ displayPhone(thirdParty?.phone) }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Telefone alternativo</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ displayPhone(thirdParty?.phoneAlternative) }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">E-mail</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ thirdParty?.email ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">E-mail alternativo</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ thirdParty?.emailAlternative || '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Indicação</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ thirdParty?.indication || '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Observações</div>
                <div class="mt-1 text-sm text-gray-700 whitespace-pre-wrap wrap-break-word">
                    {{ thirdParty?.observation || '-' }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatCnpj, formatCellphone } from '@/helpers/masks';

const props = defineProps({
    thirdParty: {
        type: Object,
        default: () => ({}),
    },
});

const serviceList = computed(() => {
    if (!Array.isArray(props.thirdParty?.services)) {
        return [];
    }
    return props.thirdParty.services;
});

const displayCnpj = (raw) => {
    if (!raw) {
        return '-';
    }
    const digits = String(raw).replace(/\D/g, '');
    if (digits === '') {
        return '-';
    }
    return formatCnpj(digits);
};

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

const activeLabel = (active) => {
    if (active === true || active === 1 || active === '1') {
        return 'Sim';
    }
    return 'Não';
};
</script>
