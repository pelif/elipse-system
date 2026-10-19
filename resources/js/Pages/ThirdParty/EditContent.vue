<template>
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Editar terceiro</h4>
                <p class="text-sm text-gray-500 mt-1">Altere os dados e salve para atualizar.</p>
            </div>
        </div>
    </div>

    <form class="p-6 pt-2 max-h-[70vh] overflow-y-auto" @submit.prevent="submit">
        <div class="w-full space-y-4">
            <div class="group-form grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-tp-name" class="input-label">Nome</label>
                    <input
                        id="modal-edit-tp-name"
                        v-model="form.name"
                        type="text"
                        name="name"
                        class="input-text"
                        maxlength="255"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.name" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.name }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-tp-alias" class="input-label">Apelido</label>
                    <input
                        id="modal-edit-tp-alias"
                        v-model="form.alias"
                        type="text"
                        name="alias"
                        class="input-text"
                        maxlength="255"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.alias" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.alias }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <MultiSelectChips
                        input-id="modal-edit-tp-services"
                        label="Especialidades"
                        v-model="form.service_ids"
                        :options="serviceSelectOptions"
                        :error="form.errors.service_ids"
                        placeholder="Selecione os serviços"
                    />
                </div>
                <div class="input-container">
                    <label for="modal-edit-tp-phone" class="input-label">Telefone</label>
                    <input
                        id="modal-edit-tp-phone"
                        v-model="form.phone"
                        type="text"
                        name="phone"
                        class="input-text"
                        maxlength="20"
                        inputmode="numeric"
                        autocomplete="off"
                        @input="onPhoneInput"
                    />
                    <div v-if="form.errors.phone" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.phone }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="modal-edit-tp-phone-alt" class="input-label">Telefone alternativo</label>
                    <input
                        id="modal-edit-tp-phone-alt"
                        v-model="form.phone_alternative"
                        type="text"
                        name="phone_alternative"
                        class="input-text"
                        maxlength="20"
                        inputmode="numeric"
                        autocomplete="off"
                        @input="onPhoneAltInput"
                    />
                    <div v-if="form.errors.phone_alternative" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.phone_alternative }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="modal-edit-tp-email" class="input-label">E-mail</label>
                    <input
                        id="modal-edit-tp-email"
                        v-model="form.email"
                        type="email"
                        name="email"
                        class="input-text"
                        maxlength="120"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.email" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.email }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="modal-edit-tp-email-alt" class="input-label">E-mail alternativo</label>
                    <input
                        id="modal-edit-tp-email-alt"
                        v-model="form.email_alternative"
                        type="email"
                        name="email_alternative"
                        class="input-text"
                        maxlength="120"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.email_alternative" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.email_alternative }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-tp-indication" class="input-label">Indicação</label>
                    <input
                        id="modal-edit-tp-indication"
                        v-model="form.indication"
                        type="text"
                        name="indication"
                        class="input-text"
                        maxlength="120"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.indication" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.indication }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-tp-observation" class="input-label">Observações</label>
                    <textarea
                        id="modal-edit-tp-observation"
                        v-model="form.observation"
                        name="observation"
                        class="input-text"
                        rows="3"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.observation" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.observation }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-tp-company" class="input-label">Razão social</label>
                    <input
                        id="modal-edit-tp-company"
                        v-model="form.company_name"
                        type="text"
                        name="company_name"
                        class="input-text"
                        maxlength="150"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.company_name" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.company_name }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="modal-edit-tp-document" class="input-label">CNPJ</label>
                    <input
                        id="modal-edit-tp-document"
                        v-model="form.document"
                        type="text"
                        name="document"
                        class="input-text"
                        maxlength="18"
                        inputmode="numeric"
                        autocomplete="off"
                        @input="onCnpjInput"
                    />
                    <div v-if="form.errors.document" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.document }}
                    </div>
                </div>
                <div class="input-container flex items-center gap-2 mt-2">
                    <input id="modal-edit-tp-active" v-model="form.active" type="checkbox" class="rounded" name="active" />
                    <label for="modal-edit-tp-active" class="input-label mb-0">Ativo</label>
                    <div v-if="form.errors.active" class="input-error">{{ form.errors.active }}</div>
                </div>
            </div>
        </div>
        <div class="group-form mb-2 flex justify-end gap-2 mt-4">
            <button type="button" class="btn-submit bg-gray-700 px-4 w-2xl" @click="emit('close')">
                Cancelar
            </button>
            <button type="submit" class="btn-submit px-4 w-2xl" :disabled="form.processing">Salvar</button>
        </div>
    </form>
</template>

<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { formatCnpj, formatCellphone } from '@/helpers/masks';
import MultiSelectChips from '@/Components/MultiSelectChips.vue';

const props = defineProps({
    thirdParty: {
        type: Object,
        default: () => ({}),
    },
    serviceSelectOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    alias: '',
    phone: '',
    phone_alternative: '',
    email: '',
    email_alternative: '',
    indication: '',
    observation: '',
    company_name: '',
    document: '',
    active: true,
    service_ids: [],
});

const syncFromThirdParty = (next) => {
    if (!next || !next.id) {
        return;
    }
    let aliasVal = '';
    if (next.alias != null) {
        aliasVal = String(next.alias);
    }
    let phoneAltVal = '';
    if (next.phoneAlternative != null) {
        phoneAltVal = formatCellphone(String(next.phoneAlternative).replace(/\D/g, ''));
    }
    let emailAltVal = '';
    if (next.emailAlternative != null) {
        emailAltVal = String(next.emailAlternative);
    }
    let indicationVal = '';
    if (next.indication != null) {
        indicationVal = String(next.indication);
    }
    let observationVal = '';
    if (next.observation != null) {
        observationVal = String(next.observation);
    }
    let companyVal = '';
    if (next.companyName != null) {
        companyVal = String(next.companyName);
    }
    form.name = next.name ?? '';
    form.alias = aliasVal;
    form.phone = formatCellphone(String(next.phone ?? '').replace(/\D/g, ''));
    form.phone_alternative = phoneAltVal;
    form.email = next.email ?? '';
    form.email_alternative = emailAltVal;
    form.indication = indicationVal;
    form.observation = observationVal;
    form.company_name = companyVal;
    form.document = formatCnpj(String(next.document ?? '').replace(/\D/g, ''));
    if (next.active === true || next.active === 1 || next.active === '1') {
        form.active = true;
    } else {
        form.active = false;
    }
    const nextServiceIds = [];
    if (Array.isArray(next.serviceIds)) {
        next.serviceIds.forEach((id) => {
            nextServiceIds.push(String(id));
        });
    }
    form.service_ids = nextServiceIds;
    form.clearErrors();
};

watch(
    () => props.thirdParty,
    (next) => {
        syncFromThirdParty(next);
    },
    { immediate: true, deep: true },
);

const onCnpjInput = (event) => {
    form.document = formatCnpj(event.target.value);
};

const onPhoneInput = (event) => {
    form.phone = formatCellphone(event.target.value);
};

const onPhoneAltInput = (event) => {
    form.phone_alternative = formatCellphone(event.target.value);
};

const submit = () => {
    if (!props.thirdParty?.id) {
        return;
    }
    form
        .transform((data) => {
            const docDigits = String(data.document ?? '').replace(/\D/g, '');
            return {
                ...data,
                document: docDigits,
            };
        })
        .put(route('third-parties.update', props.thirdParty.id), {
            preserveState: false,
            preserveScroll: false,
            onSuccess: () => {
                emit('close');
            },
        });
};
</script>
