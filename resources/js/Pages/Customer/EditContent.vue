<template>
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Editar cliente</h4>
                <p class="text-sm text-gray-500 mt-1">Altere os dados e salve para atualizar.</p>
            </div>
        </div>
    </div>

    <form class="p-6 pt-2 max-h-[70vh] overflow-y-auto" @submit.prevent="submit">
        <div class="w-full space-y-4">
            <div class="group-form grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-customer-company" class="input-label">Nome fantasia</label>
                    <input
                        id="modal-edit-customer-company"
                        v-model="form.company_name"
                        type="text"
                        class="input-text"
                        maxlength="255"
                        autocomplete="off"
                    />
                    <div v-if="errors.company_name" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ errors.company_name }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-customer-legal" class="input-label">Razão social</label>
                    <input
                        id="modal-edit-customer-legal"
                        v-model="form.legal_name"
                        type="text"
                        class="input-text"
                        maxlength="255"
                        autocomplete="off"
                    />
                    <div v-if="errors.legal_name" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ errors.legal_name }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="modal-edit-customer-document" class="input-label">CNPJ</label>
                    <input
                        id="modal-edit-customer-document"
                        v-model="form.document"
                        type="text"
                        class="input-text"
                        maxlength="18"
                        autocomplete="off"
                    />
                    <div v-if="errors.document" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ errors.document }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="modal-edit-customer-code" class="input-label">Código do cliente</label>
                    <input
                        id="modal-edit-customer-code"
                        v-model="form.code_customer"
                        type="text"
                        class="input-text"
                        maxlength="10"
                        autocomplete="off"
                    />
                    <div v-if="errors.code_customer" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ errors.code_customer }}
                    </div>
                </div>
                <div class="input-container md:col-span-2 flex items-center gap-2 mt-2">
                    <input id="modal-edit-customer-active" v-model="form.active" type="checkbox" class="rounded" />
                    <label for="modal-edit-customer-active" class="input-label mb-0">Ativo</label>
                    <div v-if="errors.active" class="input-error">{{ errors.active }}</div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-customer-agent" class="input-label">Comissionado</label>
                    <select id="modal-edit-customer-agent" v-model="form.comissioned_agent_id" class="input-text">
                        <option value="">—</option>
                        <option
                            v-for="opt in agentSelectRows"
                            :key="opt.id"
                            :value="opt.id"
                            :selected="customer.comissioned_agent_id === opt.id"
                        >
                            {{ opt.label }}
                        </option>
                    </select>
                    <div v-if="errors.comissioned_agent_id" class="input-error">{{ errors.comissioned_agent_id }}</div>
                </div>
            </div>
        </div>

        <div class="group-form mb-2 flex justify-end gap-2 mt-4">
            <button type="button" class="btn-submit bg-gray-700 px-4 w-2xl" @click="emit('close')">
                Cancelar
            </button>
            <button type="submit" class="btn-submit px-4 w-2xl" :disabled="processing">
                Salvar
            </button>
        </div>
    </form>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    customer: {
        type: Object,
        default: () => ({}),
    },
    comissionedAgentOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const agentSelectRows = computed(() =>
    props.comissionedAgentOptions
        .filter((row) => row.id != null && String(row.id).trim() !== '')
        .map((row) => ({
            id: String(row.id),
            label: row.label ?? row.name ?? String(row.id),
        })),
);

const form = reactive({
    company_name: '',
    legal_name: '',
    document: '',
    code_customer: '',
    active: true,
    comissioned_agent_id: '',
});

const errors = ref({});
const processing = ref(false);

const syncFromCustomer = (next) => {
    if (!next?.id) {
        return;
    }
    form.company_name = next.companyName ?? '';
    form.legal_name = next.legalName ?? '';
    form.document = next.document ?? '';
    form.code_customer = next.codeCustomer ?? '';
    form.active = Boolean(next.active);
    form.comissioned_agent_id = next.comissionedAgentId ?? '';
};

watch(
    () => props.customer,
    (next) => syncFromCustomer(next),
    { immediate: true, deep: true },
);

const payload = () => ({
    company_name: form.company_name,
    legal_name: form.legal_name,
    document: form.document,
    code_customer: form.code_customer,
    active: form.active,
    comissioned_agent_id: form.comissioned_agent_id || null,
});

const submit = () => {
    if (!props.customer?.id) {
        return;
    }
    processing.value = true;
    errors.value = {};
    router.visit(route('customers.update', props.customer.id), {
        method: 'put',
        data: payload(),
        preserveState: false,
        preserveScroll: false,
        onFinish: () => {
            processing.value = false;
        },
        onSuccess: () => {
            emit('close');
        },
        onError: (errs) => {
            errors.value = errs ?? {};
        },
    });
};
</script>
