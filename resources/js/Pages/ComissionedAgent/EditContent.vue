<template>
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Editar comissionado</h4>
                <p class="text-sm text-gray-500 mt-1">Altere os dados e salve para atualizar.</p>
            </div>
        </div>
    </div>

    <form class="p-6 pt-2" @submit.prevent="submit">
        <div class="w-full space-y-4">
            <div class="group-form">
                <div class="input-container">
                    <label for="edit-comissioned-name" class="input-label">Nome</label>
                    <input
                        id="edit-comissioned-name"
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
            </div>

            <div class="group-form">
                <div class="input-container">
                    <label for="edit-comissioned-default-comission" class="input-label">Comissão Padrão (%)</label>
                    <input
                        id="edit-comissioned-default-comission"
                        v-model="form.default_comission"
                        type="number"
                        name="default_comission"
                        class="input-text"
                        min="0"
                        max="100"
                        step="0.01"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.default_comission" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.default_comission }}
                    </div>
                </div>
            </div>

            <div class="group-form">
                <div class="input-container">
                    <label for="edit-comissioned-payment-days" class="input-label">Dias de Pagamento</label>
                    <input
                        id="edit-comissioned-payment-days"
                        v-model="form.payment_days"
                        type="number"
                        name="payment_days"
                        class="input-text"
                        min="0"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.payment_days" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.payment_days }}
                    </div>
                </div>
            </div>

            <div class="group-form">
                <div class="input-container">
                    <label for="edit-comissioned-observations" class="input-label">Observações</label>
                    <textarea
                        id="edit-comissioned-observations"
                        v-model="form.observations"
                        name="observations"
                        class="input-text"
                        rows="4"
                    ></textarea>
                    <div v-if="form.errors.observations" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.observations }}
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
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    comissionedAgent: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    name: '',
    default_comission: '',
    payment_days: '',
    observations: '',
});

watch(
    () => props.comissionedAgent,
    (next) => {
        if (next?.id) {
            form.name = next.name ?? '';
            form.default_comission = next.defaultComission ?? '';
            form.payment_days = next.paymentDays ?? '';
            form.observations = next.observations ?? '';
            form.clearErrors();
        }
    },
    { immediate: true, deep: true },
);

const submit = () => {
    if (!props.comissionedAgent?.id) {
        return;
    }
    form.put(route('comissioned-agents.update', props.comissionedAgent.id), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
