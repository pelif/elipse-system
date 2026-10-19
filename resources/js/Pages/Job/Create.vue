<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-video" class="mr-2" />
                    Novo job
                </h4>
                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('jobs.index')"
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
                            <SearchSelect
                                v-model="form.customer_id"
                                input-id="job-customer"
                                label="Cliente"
                                :endpoint="customerEndpoint"
                                :error-text="form.errors.customer_id"
                                @selected="onCustomerSelected"
                            />
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-title" class="input-label">Título</label>
                            <input
                                id="job-title"
                                v-model="form.job_title"
                                type="text"
                                class="input-text"
                                maxlength="255"
                            />
                            <div v-if="form.errors.job_title" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.job_title }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <SearchSelect
                                :key="contractorSelectKey"
                                v-model="form.contractor_id"
                                input-id="job-contractor"
                                label="Contratante"
                                :endpoint="contractorEndpoint"
                                :extra-params="contractorParams"
                                :disabled="contractorDisabled"
                                :placeholder="contractorPlaceholder"
                                :selected-label="contractorLabel"
                                :error-text="form.errors.contractor_id"
                            />
                            <button
                                type="button"
                                class="text-sm font-semibold mt-2"
                                :class="newContractorClass"
                                :disabled="contractorDisabled"
                                @click="openContractorModal"
                            >
                                Cadastrar Novo
                            </button>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <SearchSelect
                                v-model="form.product_id"
                                input-id="job-product"
                                label="Produto"
                                :endpoint="productEndpoint"
                                :error-text="form.errors.product_id"
                            />
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-code" class="input-label">Código do job</label>
                            <input
                                id="job-code"
                                v-model="form.job_code"
                                type="text"
                                class="input-text"
                                maxlength="20"
                            />
                            <div v-if="form.errors.job_code" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.job_code }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-os" class="input-label">Quantidade de OS</label>
                            <input
                                id="job-os"
                                v-model.number="form.os_quantity"
                                type="number"
                                min="0"
                                step="1"
                                class="input-text"
                            />
                            <div v-if="form.errors.os_quantity" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.os_quantity }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form col-span-2">
                        <div class="input-container">
                            <label for="job-briefing" class="input-label">Briefing (URL)</label>
                            <input
                                id="job-briefing"
                                v-model="form.briefing_url"
                                type="url"
                                class="input-text"
                                maxlength="500"
                                placeholder="https://exemplo.com/briefing"
                                @blur="validateBriefing"
                            />
                            <div v-if="briefingErrorText" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ briefingErrorText }}
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="font-bold text-gray-700 text-xl mt-8 px-2">Informações comerciais e financeiras</h5>

                <div class="container-form">
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-po" class="input-label">Pedido de compra</label>
                            <input
                                id="job-po"
                                v-model="form.purchase_order"
                                type="text"
                                class="input-text"
                                maxlength="255"
                            />
                            <div v-if="form.errors.purchase_order" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.purchase_order }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-po-link" class="input-label">Link do pedido</label>
                            <input
                                id="job-po-link"
                                v-model="form.purchase_order_link"
                                type="text"
                                class="input-text"
                                maxlength="10000"
                            />
                            <div v-if="form.errors.purchase_order_link" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.purchase_order_link }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-budget" class="input-label">Orçamento alvo</label>
                            <input
                                id="job-budget"
                                :value="form.target_budget"
                                type="text"
                                class="input-text"
                                inputmode="numeric"
                                autocomplete="off"
                                @input="onBudgetInput"
                            />
                            <div v-if="form.errors.target_budget" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.target_budget }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-invoice" class="input-label">Data de faturamento</label>
                            <input
                                id="job-invoice"
                                :value="form.invoice_due_date"
                                type="text"
                                class="input-text"
                                maxlength="10"
                                placeholder="dd/mm/aaaa"
                                inputmode="numeric"
                                autocomplete="off"
                                @input="onInvoiceInput"
                            />
                            <div v-if="form.errors.invoice_due_date" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.invoice_due_date }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form col-span-2">
                        <div class="input-container">
                            <label for="job-info" class="input-label">Informações complementares</label>
                            <textarea
                                id="job-info"
                                v-model="form.additional_info"
                                class="input-text"
                                rows="4"
                                maxlength="10000"
                            />
                            <div v-if="form.errors.additional_info" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.additional_info }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <SearchSelect
                                v-model="form.comissioned_agent_id"
                                input-id="job-agent"
                                label="Comissionado"
                                :endpoint="agentEndpoint"
                                :error-text="form.errors.comissioned_agent_id"
                            />
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-commission" class="input-label">% comissão</label>
                            <input
                                id="job-commission"
                                v-model="form.commissioned_percentage"
                                type="number"
                                class="input-text"
                                min="0"
                                max="999.9999"
                                step="0.0001"
                            />
                            <div v-if="form.errors.commissioned_percentage" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.commissioned_percentage }}
                            </div>
                        </div>
                    </div>
                    <div class="group-form">
                        <div class="input-container">
                            <label for="job-payment" class="input-label">Previsão de pagamento</label>
                            <input
                                id="job-payment"
                                :value="form.commission_payment_due_date"
                                type="text"
                                class="input-text"
                                maxlength="10"
                                placeholder="dd/mm/aaaa"
                                inputmode="numeric"
                                autocomplete="off"
                                @input="onPaymentInput"
                            />
                            <div v-if="form.errors.commission_payment_due_date" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.commission_payment_due_date }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group-form mb-2 flex justify-end mt-6">
                    <Link type="button" class="btn-submit mr-2 bg-gray-700" :href="route('jobs.index')">
                        Cancelar
                    </Link>
                    <button type="submit" class="btn-submit" :disabled="submitDisabled">
                        Salvar
                    </button>
                </div>
            </form>
        </div>

        <ViewModal
            v-model="showContractorModal"
            :close-on-esc="true"
            :close-on-back-drop="true"
            max-width="2xl"
        >
            <div class="p-6">
                <h4 class="font-bold text-gray-700 text-2xl mb-4">Novo contratante</h4>
                <p class="text-gray-500 mb-4">Cliente: {{ customerLabel }}</p>
                <div class="input-container">
                    <label for="new-contractor-name" class="input-label">Nome</label>
                    <input id="new-contractor-name" v-model="contractorForm.name" type="text" class="input-text" maxlength="255" />
                    <div v-if="contractorError('name')" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ contractorError('name') }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="new-contractor-phone" class="input-label">Telefone</label>
                    <input
                        id="new-contractor-phone"
                        :value="contractorForm.phone"
                        type="text"
                        class="input-text"
                        maxlength="20"
                        inputmode="numeric"
                        autocomplete="off"
                        @input="onContractorPhone"
                    />
                    <div v-if="contractorError('phone')" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ contractorError('phone') }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="new-contractor-email" class="input-label">E-mail</label>
                    <input id="new-contractor-email" v-model="contractorForm.email" type="email" class="input-text" maxlength="120" />
                    <div v-if="contractorError('email')" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ contractorError('email') }}
                    </div>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" class="btn-submit mr-2 bg-gray-700" @click="closeContractorModal">
                        Cancelar
                    </button>
                    <button type="button" class="btn-submit" :disabled="contractorProcessing" @click="saveContractor">
                        Salvar
                    </button>
                </div>
            </div>
        </ViewModal>
    </MainLayout>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import SearchSelect from '@/Components/SearchSelect.vue';
import ViewModal from '@/Components/ViewModal.vue';
import { formatCellphone } from '@/helpers/masks';

const page = usePage();

const pageTitle = 'Jobs';
const pageType = 'Fluxo de Trabalho';

const customerEndpoint = route('customers.for-select');
const contractorEndpoint = route('contractors.for-select');
const productEndpoint = route('products.for-select');
const agentEndpoint = route('comissioned-agents.for-select');

const form = useForm({
    customer_id: '',
    contractor_id: '',
    job_code: '',
    job_title: '',
    product_id: '',
    os_quantity: 0,
    briefing_url: '',
    purchase_order: '',
    purchase_order_link: '',
    target_budget: '0,00',
    additional_info: '',
    invoice_due_date: '',
    comissioned_agent_id: '',
    commissioned_percentage: '',
    commission_payment_due_date: '',
});

const customerLabel = ref('');
const contractorLabel = ref('');
const briefingClientError = ref('');
const showContractorModal = ref(false);
const contractorProcessing = ref(false);
const contractorErrors = ref({});
const contractorForm = reactive({
    name: '',
    phone: '',
    email: '',
});

let codeRequest = 0;

const contractorParams = computed(() => {
    return {
        customer_id: form.customer_id,
    };
});

const contractorSelectKey = computed(() => form.customer_id);

const contractorDisabled = computed(() => form.customer_id.trim() === '');

const contractorPlaceholder = computed(() => {
    if (contractorDisabled.value) {
        return 'Selecione o cliente antes';
    }

    return 'Digite para buscar';
});

const newContractorClass = computed(() => {
    if (contractorDisabled.value) {
        return 'text-gray-400 cursor-not-allowed';
    }

    return 'text-amber-700 hover:opacity-80 cursor-pointer';
});

const submitDisabled = computed(() => {
    if (form.processing) {
        return true;
    }
    if (form.customer_id.trim() === '') {
        return true;
    }
    if (form.contractor_id.trim() === '') {
        return true;
    }
    if (form.job_code.trim() === '') {
        return true;
    }
    if (form.job_title.trim() === '') {
        return true;
    }
    if (form.product_id.trim() === '') {
        return true;
    }

    return false;
});

const briefingErrorText = computed(() => {
    if (briefingClientError.value !== '') {
        return briefingClientError.value;
    }
    if (form.errors.briefing_url) {
        return form.errors.briefing_url;
    }

    return '';
});

const onCustomerSelected = (option) => {
    if (option && option.label) {
        customerLabel.value = option.label;
        return;
    }
    customerLabel.value = '';
};

const loadNextCode = (customerId) => {
    const current = ++codeRequest;
    if (!customerId) {
        form.job_code = '';
        return;
    }

    window.axios
        .get(route('jobs.next-code'), {
            params: { customer_id: customerId },
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            if (current !== codeRequest) {
                return;
            }
            let code = '';
            if (response.data && response.data.job_code) {
                code = response.data.job_code;
            }
            form.job_code = code;
        })
        .catch((error) => {
            if (current !== codeRequest) {
                return;
            }
            form.job_code = '';
            let message = 'Não foi possível sugerir o código do job.';
            if (error.response && error.response.data && error.response.data.message) {
                message = error.response.data.message;
            }
            form.setError('job_code', message);
        });
};

watch(
    () => form.customer_id,
    (customerId) => {
        form.contractor_id = '';
        contractorLabel.value = '';
        form.clearErrors('job_code');
        loadNextCode(customerId);
    },
);

const formatTargetBudget = (raw) => {
    const digits = String(raw ?? '').replace(/\D/g, '');
    let padded = digits;
    if (padded === '') {
        padded = '0';
    }
    while (padded.length < 3) {
        padded = '0' + padded;
    }
    const cents = padded.slice(-2);
    let integer = padded.slice(0, -2).replace(/^0+(?=\d)/, '');
    if (integer === '') {
        integer = '0';
    }
    const parts = [];
    let rest = integer;
    while (rest.length > 3) {
        parts.unshift(rest.slice(-3));
        rest = rest.slice(0, -3);
    }
    parts.unshift(rest);

    return parts.join('.') + ',' + cents;
};

const formatDateBr = (raw) => {
    const digits = String(raw ?? '').replace(/\D/g, '').slice(0, 8);
    if (digits.length <= 2) {
        return digits;
    }
    if (digits.length <= 4) {
        return digits.slice(0, 2) + '/' + digits.slice(2);
    }

    return digits.slice(0, 2) + '/' + digits.slice(2, 4) + '/' + digits.slice(4);
};

const onBudgetInput = (event) => {
    form.target_budget = formatTargetBudget(event.target.value);
};

const onInvoiceInput = (event) => {
    form.invoice_due_date = formatDateBr(event.target.value);
};

const onPaymentInput = (event) => {
    form.commission_payment_due_date = formatDateBr(event.target.value);
};

const validateBriefing = () => {
    const value = form.briefing_url.trim();
    if (value === '') {
        briefingClientError.value = '';
        return true;
    }
    const hasScheme = value.startsWith('http://') || value.startsWith('https://');
    if (!hasScheme || value.indexOf('.') === -1) {
        briefingClientError.value = 'Informe uma URL com http:// ou https:// e um ponto.';
        return false;
    }
    if (value.length > 500) {
        briefingClientError.value = 'A URL de briefing deve ter no máximo 500 caracteres.';
        return false;
    }
    briefingClientError.value = '';

    return true;
};

const submit = () => {
    if (submitDisabled.value) {
        return;
    }
    if (!validateBriefing()) {
        return;
    }
    form.post(route('jobs.store'));
};

const openContractorModal = () => {
    if (contractorDisabled.value) {
        return;
    }
    contractorErrors.value = {};
    contractorForm.name = '';
    contractorForm.phone = '';
    contractorForm.email = '';
    showContractorModal.value = true;
};

const closeContractorModal = () => {
    showContractorModal.value = false;
};

const onContractorPhone = (event) => {
    contractorForm.phone = formatCellphone(event.target.value);
};

const contractorError = (field) => {
    const value = contractorErrors.value[field];
    if (!value) {
        return '';
    }
    if (Array.isArray(value) && value.length > 0) {
        return value[0];
    }

    return String(value);
};

const saveContractor = () => {
    contractorProcessing.value = true;
    contractorErrors.value = {};
    window.axios
        .post(
            route('contractors.store'),
            {
                name: contractorForm.name,
                phone: contractorForm.phone,
                email: contractorForm.email,
                customer_id: form.customer_id,
            },
            { headers: { Accept: 'application/json' } },
        )
        .then((response) => {
            const created = response.data;
            if (created && created.id) {
                let label = '';
                if (created.name) {
                    label = created.name;
                }
                contractorLabel.value = label;
                form.contractor_id = created.id;
                showContractorModal.value = false;
            }
        })
        .catch((error) => {
            if (error.response && error.response.status === 422 && error.response.data && error.response.data.errors) {
                contractorErrors.value = error.response.data.errors;
                return;
            }
            contractorErrors.value = { name: ['Não foi possível cadastrar o contratante.'] };
        })
        .finally(() => {
            contractorProcessing.value = false;
        });
};
</script>
