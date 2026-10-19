<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div
            class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
        >
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-user" class="mr-2" />
                    Novo cliente
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('customers.index')"
                >
                    Voltar
                </Link>
            </div>

            <form class="p-6 max-h-[calc(100vh-12rem)] overflow-y-auto" @submit.prevent="submit">
                <div v-if="page.flash.message" class="success-message">
                    <font-awesome-icon icon="fa-solid fa-check" class="mr-2 text-2xl" />
                    {{ page.flash.message }}
                </div>
                <div v-if="page.flash.error" class="error-message">
                    <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                    {{ page.flash.error }}
                </div>

                <div class="container-form">
                    <div class="group-form">
                        <div class="input-container">
                            <label for="customer-company" class="input-label">Nome fantasia</label>
                            <input id="customer-company" v-model="form.company_name" type="text" class="input-text" maxlength="255" />
                            <div v-if="errors.company_name" class="input-error">{{ errors.company_name }}</div>
                        </div>
                        <div class="input-container">
                            <label for="customer-legal" class="input-label">Razão social</label>
                            <input id="customer-legal" v-model="form.legal_name" type="text" class="input-text" maxlength="255" />
                            <div v-if="errors.legal_name" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ errors.legal_name }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="customer-document" class="input-label">CNPJ</label>
                            <input
                                id="customer-document"
                                v-model="form.document"
                                type="text"
                                class="input-text"
                                maxlength="18"
                                inputmode="numeric"
                                autocomplete="off"
                                @input="maskCnpj"
                            />
                            <div v-if="errors.document" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ errors.document }}
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="customer-code" class="input-label">Código do cliente</label>
                            <input id="customer-code" v-model="form.code_customer" type="text" class="input-text" maxlength="10" />
                            <div v-if="errors.code_customer" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ errors.code_customer }}
                            </div>
                        </div>
                    </div>

                    <div class="group-form">
                        <div class="input-container">
                            <label for="customer-agent" class="input-label">Comissionado</label>
                            <select id="customer-agent" v-model="form.comissioned_agent_id" class="input-text">
                                <option value="">—</option>
                                <option v-for="opt in agentSelectRows" :key="opt.id" :value="opt.id">{{ opt.label }}</option>
                            </select>
                            <div v-if="errors.comissioned_agent_id" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ errors.comissioned_agent_id }}
                            </div>
                        </div>
                    </div>

                    <div class="group-form" aria-hidden="true"></div>
                </div>

                <div class="group-form mb-2 flex justify-end mt-6">
                    <Link type="button" class="btn-submit mr-2 bg-gray-700" :href="route('customers.index')">
                        Cancelar
                    </Link>
                    <button type="submit" class="btn-submit" :disabled="processing">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import { formatCnpj } from '@/helpers/masks';

const page = usePage();

const props = defineProps({
    comissionedAgentOptions: {
        type: Array,
        default: () => [],
    },
});

const pageTitle = 'Clientes';
const pageType = 'Cadastros';

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

const payload = () => {
    return {
        company_name: form.company_name,
        legal_name: form.legal_name,
        document: form.document.replace(/\D/g, ''),
        code_customer: form.code_customer,
        active: form.active,
        comissioned_agent_id: form.comissioned_agent_id || null,
    };
};


const submit = () => {
    processing.value = true;
    errors.value = {};
    router.visit(route('customers.store'), {
        method: 'post',
        data: payload(),
        preserveState: false,
        preserveScroll: false,
        onFinish: () => {
            processing.value = false;
        },
        onError: (errs) => {
            errors.value = errs ?? {};
        },
    });
};

const maskCnpj = (event) => {
    form.document = formatCnpj(event.target.value);
}
</script>
