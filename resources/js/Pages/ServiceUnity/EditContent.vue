<template>
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Editar Unidade de Serviço</h4>
                <p class="text-sm text-gray-500 mt-1">Altere os dados e salve para atualizar.</p>
            </div>
        </div>
    </div>

    <form class="p-6 pt-2" @submit.prevent="submit">
        <div class="w-full space-y-4">
            <div class="group-form">
                <div class="input-container">
                    <label for="modal-edit-unity-name" class="input-label">Unidade de Serviço</label>
                    <input
                        id="modal-edit-unity-name"
                        v-model="form.unity"
                        type="text"
                        name="unity"
                        class="input-text"
                        maxlength="255"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.unity" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.unity }}
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
    serviceUnity: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    unity: '',
});

watch(
    () => props.serviceUnity,
    (next) => {
        if (next?.id) {
            form.unity = next.unity ?? '';
            form.clearErrors();
        }
    },
    { immediate: true, deep: true },
);

const submit = () => {
    if (!props.serviceUnity?.id) {
        return;
    }
    form.put(route('unity-service.update', props.serviceUnity.id), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
