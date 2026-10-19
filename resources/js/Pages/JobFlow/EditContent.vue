<template>
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Editar Status de Job</h4>
                <p class="text-sm text-gray-500 mt-1">Altere os campos e salve para atualizar.</p>
            </div>
        </div>
    </div>

    <form class="p-6 pt-2" @submit.prevent="submit">
        <div class="container-form">
            <div class="group-form">
                <div class="input-container">
                    <label for="modal-edit-status" class="input-label">Status</label>
                    <input
                        id="modal-edit-status"
                        v-model="form.status"
                        type="text"
                        name="status"
                        class="input-text"
                        maxlength="50"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.status" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.status }}
                    </div>
                </div>
            </div>

            <div class="group-form">
                <div class="input-container">
                    <label for="modal-edit-observacoes" class="input-label">Observações</label>
                    <textarea
                        id="modal-edit-observacoes"
                        v-model="form.observacoes"
                        name="observacoes"
                        class="input-text"
                        maxlength="500"
                        rows="4"
                    />
                    <div v-if="form.errors.observacoes" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.observacoes }}
                    </div>
                </div>
            </div>
        </div>
        <div class="group-form mb-2 flex justify-end gap-2 mt-4">
            <button type="button" class="btn-submit bg-gray-700 px-4 w-2xl" @click="emit('close')">Cancelar</button>
            <button type="submit" class="btn-submit px-4 w-2xl" :disabled="form.processing">Salvar</button>
        </div>
    </form>
</template>

<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    status: '',
    observacoes: '',
});

watch(
    () => props.status,
    (next) => {
        if (next?.id) {
            form.status = next.status ?? '';
            form.observacoes = next.observacoes ?? '';
            form.clearErrors();
        }
    },
    { immediate: true, deep: true }
);

const submit = () => {
    if (!props.status?.id) {
        return;
    }
    form.put(route('job-flow.update', props.status.id), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
