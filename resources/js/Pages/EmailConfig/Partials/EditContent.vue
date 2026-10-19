<template>
    <div class="p-6">
        <form @submit.prevent="submit">
            <div class="container-form">
                <div class="group-form">
                    <div class="input-container">
                        <label for="address" class="input-label">E-mail</label>
                        <input
                            id="address"
                            v-model="form.address"
                            type="text"
                            name="address"
                            class="input-text"
                            maxlength="255"
                            autocomplete="off"
                        />
                        <div v-if="form.errors.address" class="input-error">
                            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                            {{ form.errors.address }}
                        </div>
                    </div>
                </div>

                <div class="group-form">
                    <div class="input-container">
                        <label for="department_id" class="input-label">Departamento</label>
                        <select id="department_id" v-model="form.department_id" class="input-text">
                            <option value="" disabled>Selecione</option>
                            <option v-for="d in departments" :key="d.id" :value="d.id">
                                {{ d.department }}
                            </option>
                        </select>
                        <div v-if="form.errors.department_id" class="input-error">
                            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                            {{ form.errors.department_id }}
                        </div>
                    </div>
                </div>

                <div class="group-form"></div>

                <div class="group-form">
                    <div class="group-form mb-2 flex justify-end">
                        <button type="button" class="btn-submit bg-gray-700 px-4 w-2xl" @click="$emit('close')">
                            Cancelar
                        </button>
                        <button type="submit" class="btn-submit px-4 w-2xl" :disabled="form.processing">
                            Salvar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const emit = defineEmits(['close']);

const props = defineProps({
    emailConfig: {
        type: Object,
        required: true
    },
    departments: {
        type: Array,
        default: () => []
    },
});

const form = useForm({
    address: props.emailConfig.address ?? '',
    department_id: props.emailConfig.departmentId ?? '',
});

const submit = () => {
    form.put(route('email-config.update', props.emailConfig.id), {
        preserveState: false,
        onSuccess: () => emit('close'),
    });
};
</script>

