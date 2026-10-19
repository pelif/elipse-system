<template>
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Editar serviço</h4>
                <p class="text-sm text-gray-500 mt-1">Altere os dados e salve para atualizar.</p>
            </div>
        </div>
    </div>

    <form class="p-6 pt-2" @submit.prevent="submit">
        <div class="w-full space-y-4">
            <div class="group-form">
                <div class="input-container">
                    <label for="modal-edit-service-name" class="input-label">Serviço</label>
                    <input
                        id="modal-edit-service-name"
                        v-model="form.service"
                        type="text"
                        name="service"
                        class="input-text"
                        maxlength="255"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.service" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.service }}
                    </div>
                </div>

                <div class="mt-4">
                    <SelectServiceUnity
                        label="Unidade"
                        input-id="modal-edit-service-unity"
                        v-model="form.unity_id"
                        :options="serviceUnitySelectOptions"
                        placeholder="Selecione a unidade"
                        :error="form.errors.unity_id"
                    />
                </div>

                <div class="input-container mt-4">
                    <label for="modal-edit-service-description" class="input-label">Descrição</label>
                    <textarea
                        id="modal-edit-service-description"
                        v-model="form.description"
                        name="description"
                        class="input-text"
                        maxlength="500"
                        rows="4"
                    />
                    <div v-if="form.errors.description" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.description }}
                    </div>
                </div>
            </div>
        </div>
        <div class="group-form mb-2 flex justify-end gap-2 mt-4">
            <button type="button" class="btn-submit bg-gray-700 px-4 w-2xl" @click="emit('close')">
                Cancelar
            </button>
            <button type="submit" class="btn-submit px-4 w-2xl" :disabled="form.processing">
                Salvar
            </button>
        </div>
    </form>
</template>

<script setup>
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import SelectServiceUnity from '@/Components/SelectServiceUnity.vue';

const props = defineProps({
    service: {
        type: Object,
        default: () => ({}),
    },
    serviceUnitySelectOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    service: '',
    unity_id: '',
    description: '',
});

watch(
    () => props.service,
    (next) => {
        if (next?.id) {
            form.service = next.service ?? '';
            form.description = next.description ?? '';
            form.unity_id = '';
            if (next.unityId) {
                form.unity_id = String(next.unityId);
            }
            form.clearErrors();
        }
    },
    { immediate: true, deep: true },
);

const submit = () => {
    if (!props.service?.id) {
        return;
    }
    form.put(route('services.update', props.service.id), {
        preserveState: false,
        preserveScroll: false,
        onSuccess: () => {
            emit('close');
        },
    });
};
</script>
