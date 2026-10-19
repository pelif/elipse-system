<template>
    <div class="p-6 border-b border-gray-100">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Editar recurso</h4>
                <p class="text-sm text-gray-500 mt-1">Altere os dados e salve para atualizar.</p>
            </div>
        </div>
    </div>

    <form class="p-6 pt-2 max-h-[70vh] overflow-y-auto" @submit.prevent="submit">
        <div class="w-full space-y-4">
            <div class="group-form grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <SelectResourceCategory
                        label="Categoria"
                        input-id="modal-edit-res-category"
                        v-model="form.category_id"
                        :options="categorySelectOptions"
                        placeholder="Selecione a categoria"
                        :error="form.errors.category_id"
                    />
                </div>
                <div class="md:col-span-2">
                    <SelectDepartment
                        label="Departamento"
                        input-id="modal-edit-res-department"
                        v-model="form.department_id"
                        :options="departmentSelectOptions"
                        placeholder="Selecione o departamento"
                        :error="form.errors.department_id"
                    />
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-res-name" class="input-label">Nome do recurso</label>
                    <input
                        id="modal-edit-res-name"
                        v-model="form.resource_name"
                        type="text"
                        name="resource_name"
                        class="input-text"
                        maxlength="150"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.resource_name" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.resource_name }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-res-pat" class="input-label">Código patrimonial</label>
                    <input
                        id="modal-edit-res-pat"
                        v-model="form.patrimonial_code"
                        type="text"
                        name="patrimonial_code"
                        class="input-text"
                        maxlength="120"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.patrimonial_code" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.patrimonial_code }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="modal-edit-res-manufacturer" class="input-label">Fabricante</label>
                    <input
                        id="modal-edit-res-manufacturer"
                        v-model="form.manufacturer"
                        type="text"
                        name="manufacturer"
                        class="input-text"
                        maxlength="150"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.manufacturer" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.manufacturer }}
                    </div>
                </div>
                <div class="input-container">
                    <label for="modal-edit-res-model" class="input-label">Modelo</label>
                    <input
                        id="modal-edit-res-model"
                        v-model="form.model"
                        type="text"
                        name="model"
                        class="input-text"
                        maxlength="255"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.model" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.model }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-res-serial" class="input-label">Número de série</label>
                    <input
                        id="modal-edit-res-serial"
                        v-model="form.serial_number"
                        type="text"
                        name="serial_number"
                        class="input-text"
                        maxlength="255"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.serial_number" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.serial_number }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-res-notes" class="input-label">Observações</label>
                    <textarea
                        id="modal-edit-res-notes"
                        v-model="form.notes"
                        name="notes"
                        class="input-text"
                        rows="3"
                        autocomplete="off"
                    />
                    <div v-if="form.errors.notes" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.notes }}
                    </div>
                </div>
                <div class="input-container md:col-span-2">
                    <label for="modal-edit-res-doc" class="input-label">Link de documentação</label>
                    <input
                        id="modal-edit-res-doc"
                        v-model="form.documentation_link"
                        type="text"
                        name="documentation_link"
                        class="input-text"
                        maxlength="500"
                        inputmode="url"
                        autocomplete="off"
                        placeholder="https://"
                        @input="onDocumentationLinkInput"
                    />
                    <div v-if="form.errors.documentation_link" class="input-error">
                        <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                        {{ form.errors.documentation_link }}
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
import { formatDocumentationUrl, normalizeDocumentationLinkForSubmit } from '@/helpers/masks';
import SelectResourceCategory from '@/Components/SelectResourceCategory.vue';
import SelectDepartment from '@/Components/SelectDepartment.vue';

const props = defineProps({
    resource: {
        type: Object,
        default: () => ({}),
    },
    categorySelectOptions: {
        type: Array,
        default: () => [],
    },
    departmentSelectOptions: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['close']);

const form = useForm({
    category_id: '',
    department_id: '',
    resource_name: '',
    patrimonial_code: '',
    manufacturer: '',
    model: '',
    serial_number: '',
    notes: '',
    documentation_link: '',
});

const syncFromResource = (next) => {
    if (!next || !next.id) {
        return;
    }
    let manufacturerVal = '';
    if (next.manufacturer != null) {
        manufacturerVal = String(next.manufacturer);
    }
    let modelVal = '';
    if (next.model != null) {
        modelVal = String(next.model);
    }
    let serialVal = '';
    if (next.serialNumber != null) {
        serialVal = String(next.serialNumber);
    }
    let notesVal = '';
    if (next.notes != null) {
        notesVal = String(next.notes);
    }
    let docVal = '';
    if (next.documentationLink != null) {
        docVal = formatDocumentationUrl(String(next.documentationLink));
    }
    form.category_id = next.categoryId ?? '';
    form.department_id = next.departmentId ?? '';
    form.resource_name = next.resourceName ?? '';
    form.patrimonial_code = next.patrimonialCode ?? '';
    form.manufacturer = manufacturerVal;
    form.model = modelVal;
    form.serial_number = serialVal;
    form.notes = notesVal;
    form.documentation_link = docVal;
    form.clearErrors();
};

watch(
    () => props.resource,
    (next) => {
        syncFromResource(next);
    },
    { immediate: true, deep: true },
);

const onDocumentationLinkInput = (event) => {
    form.documentation_link = formatDocumentationUrl(event.target.value);
};

const submit = () => {
    if (!props.resource?.id) {
        return;
    }
    form
        .transform((data) => {
            const link = normalizeDocumentationLinkForSubmit(data.documentation_link);
            const out = { ...data };
            if (link === '') {
                out.documentation_link = null;
            } else {
                out.documentation_link = link;
            }
            return out;
        })
        .put(route('resources.update', props.resource.id), {
            preserveState: false,
            preserveScroll: false,
            onSuccess: () => {
                emit('close');
            },
        });
};
</script>
