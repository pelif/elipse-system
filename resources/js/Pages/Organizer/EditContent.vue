<template>
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Editar organizador</h4>
                <p class="text-sm text-gray-500 mt-1">Altere os dados e salve para atualizar.</p>
            </div>
        </div>
    </div>

    <form class="p-6 pt-2 max-h-[70vh] overflow-y-auto" @submit.prevent="submit">
        <div class="w-full space-y-4">
            <div class="group-form grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-org-name" class="input-label">Nome do Organizador</label>
                    <input
                        id="modal-edit-org-name"
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
                <div class="input-container">
                    <label for="modal-edit-org-phone" class="input-label">Telefone de Contato</label>
                    <input
                        id="modal-edit-org-phone"
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
                    <label for="modal-edit-org-email" class="input-label">E-mail de Contato</label>
                    <input
                        id="modal-edit-org-email"
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
                <div class="input-container md:col-span-2">
                    <CustomerAutocomplete
                        v-model="form.customer_id"
                        input-id="modal-edit-org-customer"
                        label="Cliente"
                        :options="customers"
                        :fallback-display-name="fallbackCustomerName"
                        :error-text="form.errors.customer_id"
                    />
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-org-comm" class="input-label">Cuidados de comunicação</label>
                    <textarea
                        id="modal-edit-org-comm"
                        v-model="form.communication_care"
                        name="communication_care"
                        class="input-text"
                        rows="3"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.communication_care" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.communication_care }}
                    </div>
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
import { computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import CustomerAutocomplete from '@/Components/CustomerAutocomplete.vue';
import { formatCellphone } from '@/helpers/masks';

const props = defineProps({
    organizer: {
        type: Object,
        default: () => ({}),
    },
    customers: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    customer_id: '',
    phone: '',
    email: '',
    communication_care: '',
});

const fallbackCustomerName = computed(() => {
    const o = props.organizer;
    if (!o || o.customerName == null) {
        return '';
    }
    return String(o.customerName);
});

const syncFromOrganizer = (next) => {
    if (!next || !next.id) {
        return;
    }
    form.name = next.name ?? '';
    form.customer_id = next.customerId ?? '';
    const rawPhone = next.phone ?? '';
    form.phone = formatCellphone(String(rawPhone).replace(/\D/g, ''));
    form.email = next.email ?? '';
    let comm = '';
    if (next.communicationCare != null) {
        comm = String(next.communicationCare);
    }
    form.communication_care = comm;
    form.clearErrors();
};

watch(
    () => props.organizer,
    (next) => {
        syncFromOrganizer(next);
    },
    { immediate: true, deep: true },
);

const onPhoneInput = (event) => {
    form.phone = formatCellphone(event.target.value);
};

const submit = () => {
    if (!props.organizer?.id) {
        return;
    }
    form.put(route('organizers.update', props.organizer.id), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
