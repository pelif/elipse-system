<template>
    <div class="p-6 max-h-[70vh] overflow-y-auto">
        <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-4 mb-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Informações financeiras</h4>
                <p class="text-sm text-gray-500 mt-1">
                    Cliente:
                    <strong>{{ customer?.companyName ?? '-' }}</strong>
                </p>
            </div>
        </div>

        <div v-if="!customer?.id" class="text-gray-500 text-sm">Carregando...</div>

        <template v-else-if="!canManage">
            <dl class="grid grid-cols-1 gap-3 text-sm">
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase text-gray-500 font-bold">Contato financeiro</dt>
                    <dd class="mt-1 font-medium text-gray-900">{{ fields.name }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase text-gray-500 font-bold">Telefone</dt>
                    <dd class="mt-1 text-gray-800">{{ fields.phone }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase text-gray-500 font-bold">E-mail contato</dt>
                    <dd class="mt-1 text-gray-800">{{ fields.email }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase text-gray-500 font-bold">E-mail NF</dt>
                    <dd class="mt-1 text-gray-800">{{ fields.email_nf }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase text-gray-500 font-bold">Prazo de pagamento</dt>
                    <dd class="mt-1 text-gray-800">{{ fields.deadline_payment }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase text-gray-500 font-bold">Antecipação</dt>
                    <dd class="mt-1 text-gray-800">{{ fields.antecipation }}</dd>
                </div>
                <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                    <dt class="text-xs uppercase text-gray-500 font-bold">Instruções antecipação</dt>
                    <dd class="mt-1 text-gray-800 whitespace-pre-wrap">{{ fields.antecipation_instructions }}</dd>
                </div>
            </dl>
            <div class="flex justify-end mt-6">
                <button type="button" class="btn-submit bg-gray-700 px-4" @click="emit('close')">Fechar</button>
            </div>
        </template>

        <form v-else @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-container md:col-span-2">
                    <label class="input-label">Nome contato financeiro</label>
                    <input v-model="form.name" type="text" class="input-text" maxlength="255" />
                    <div v-if="fieldErrors.name" class="input-error">{{ fieldErrors.name }}</div>
                </div>
                <div class="input-container">
                    <label class="input-label">Telefone</label>
                    <input
                        v-model="form.phone"
                        type="text"
                        class="input-text"
                        maxlength="20"
                        inputmode="numeric"
                        autocomplete="off"
                        @input="maskPhone"
                    />
                    <div v-if="fieldErrors.phone" class="input-error">{{ fieldErrors.phone }}</div>
                </div>
                <div class="input-container">
                    <label class="input-label">E-mail contato</label>
                    <input v-model="form.email" type="email" class="input-text" maxlength="255" />
                    <div v-if="fieldErrors.email" class="input-error">{{ fieldErrors.email }}</div>
                </div>
                <div class="input-container md:col-span-2">
                    <label class="input-label">E-mail NF</label>
                    <input v-model="form.email_nf" type="email" class="input-text" maxlength="255" />
                    <div v-if="fieldErrors.email_nf" class="input-error">{{ fieldErrors.email_nf }}</div>
                </div>
                <div class="input-container md:col-span-2">
                    <label class="input-label">Prazo de pagamento (dias)</label>
                    <input v-model.number="form.deadline_payment" type="number" min="1" class="input-text" />
                    <div v-if="fieldErrors.deadline_payment" class="input-error">{{ fieldErrors.deadline_payment }}</div>
                </div>
                <div class="input-container md:col-span-2 flex items-center gap-2">
                    <input id="fi-antecipation" v-model="form.antecipation" type="checkbox" class="rounded" />
                    <label for="fi-antecipation" class="input-label mb-0">Antecipação</label>
                    <div v-if="fieldErrors.antecipation" class="input-error">{{ fieldErrors.antecipation }}</div>
                </div>
                <div class="input-container md:col-span-2">
                    <label class="input-label">Instruções de antecipação</label>
                    <textarea v-model="form.antecipation_instructions" class="input-text" rows="4" maxlength="500" />
                    <div v-if="fieldErrors.antecipation_instructions" class="input-error">
                        {{ fieldErrors.antecipation_instructions }}
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" class="btn-submit bg-gray-700 px-4" @click="emit('close')">Fechar</button>
                <button type="submit" class="btn-submit px-4" :disabled="saving">{{ saving ? 'Salvando…' : 'Salvar' }}</button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { formatCellphone } from '@/helpers/masks';

const props = defineProps({
    customer: {
        type: Object,
        default: () => ({}),
    },
    canManage: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close', 'saved']);

const saving = ref(false);
const fieldErrors = reactive({});

const defaultFinancialForm = () => ({
    name: '',
    phone: '',
    email: '',
    email_nf: '',
    deadline_payment: null,
    antecipation: false,
    antecipation_instructions: '',
});

const fi = computed(() => props.customer?.financialInfo ?? {});

const fields = computed(() => {
    const f = fi.value;
    const c = props.customer ?? {};
    const deadline =
        f.deadlinePayment != null ? String(f.deadlinePayment) : c.deadlinePayment != null ? String(c.deadlinePayment) : '-';
    const antRaw = f.antecipation != null ? f.antecipation : c.antecipation;
    const antLabel = Boolean(Number(antRaw ?? 0)) ? 'Sim' : 'Não';
    return {
        name: f.financialContactName ?? '-',
        phone: f.financialContactPhone ?? '-',
        email: f.financialContactEmail ?? '-',
        email_nf: f.emailNf ?? '-',
        deadline_payment: deadline,
        antecipation: antLabel,
        antecipation_instructions: f.antecipationInstructions ?? c.antecipationInstrutions ?? '-',
    };
});

const form = reactive(defaultFinancialForm());

const clearErrors = () => {
    Object.keys(fieldErrors).forEach((k) => delete fieldErrors[k]);
};

watch(
    () => props.customer,
    (next) => {
        clearErrors();
        if (!next?.id) {
            Object.assign(form, defaultFinancialForm());
            return;
        }
        const f = next.financialInfo ?? {};
        form.name = f.financialContactName ?? '';
        form.phone = f.financialContactPhone ?? '';
        form.email = f.financialContactEmail ?? '';
        form.email_nf = f.emailNf ?? '';
        form.deadline_payment =
            f.deadlinePayment != null ? Number(f.deadlinePayment) : next.deadlinePayment != null ? Number(next.deadlinePayment) : null;
        const antRaw = f.antecipation != null ? f.antecipation : next.antecipation;
        form.antecipation = Boolean(Number(antRaw ?? 0));
        form.antecipation_instructions = f.antecipationInstructions ?? next.antecipationInstrutions ?? '';
    },
    { immediate: true, deep: true },
);

const firstError = (errs) => (Array.isArray(errs) ? errs[0] : errs);

const maskPhone = (event) => {
    form.phone = formatCellphone(event.target.value);
}

const submit = async () => {

    if (!props.customer?.id || props.canManage !== true)
        return;

    clearErrors();
    saving.value = true;

    try {
        await window.axios.put(
            route('customers.update-financial', props.customer.id),
            {
                name: form.name,
                phone: form.phone.replace(/\D/g, ''),
                email: form.email,
                email_nf: form.email_nf,
                deadline_payment: form.deadline_payment,
                antecipation: form.antecipation,
                antecipation_instructions: form.antecipation_instructions,
            },
            { headers: { Accept: 'application/json' } },
        );
        emit('saved');
        emit('close');
        router.reload({ preserveScroll: true });
    } catch (e) {
        const errs = e.response?.data?.errors ?? {};
        Object.keys(errs).forEach((key) => {
            fieldErrors[key] = firstError(errs[key]);
        });
        if (!Object.keys(fieldErrors).length && e.response?.data?.message) {
            fieldErrors.general = e.response.data.message;
        }
    } finally {
        saving.value = false;
    }
};
</script>
