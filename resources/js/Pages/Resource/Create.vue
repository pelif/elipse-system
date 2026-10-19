<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />

        <div
            class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
        >
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-list" class="mr-2" />
                    Novo recurso
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('resources.index')"
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

                <div class="container-form space-y-6">
                    <div class="group-form grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <SelectResourceCategory
                                label="Categoria"
                                input-id="res-create-category"
                                v-model="form.category_id"
                                :options="categorySelectOptions"
                                placeholder="Selecione a categoria"
                                :error="form.errors.category_id"
                            />
                        </div>
                        <div class="md:col-span-2">
                            <SelectDepartment
                                label="Departamento"
                                input-id="res-create-department"
                                v-model="form.department_id"
                                :options="departmentSelectOptions"
                                placeholder="Selecione o departamento"
                                :error="form.errors.department_id"
                            />
                        </div>
                        <div class="input-container md:col-span-2">
                            <label for="res-create-name" class="input-label">Nome do recurso</label>
                            <input
                                id="res-create-name"
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
                            <label for="res-create-pat" class="input-label">Código patrimonial</label>
                            <input
                                id="res-create-pat"
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
                            <label for="res-create-manufacturer" class="input-label">Fabricante</label>
                            <input
                                id="res-create-manufacturer"
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
                            <label for="res-create-model" class="input-label">Modelo</label>
                            <input
                                id="res-create-model"
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
                            <label for="res-create-serial" class="input-label">Número de série</label>
                            <input
                                id="res-create-serial"
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
                            <label for="res-create-notes" class="input-label">Observações</label>
                            <textarea
                                id="res-create-notes"
                                v-model="form.notes"
                                name="notes"
                                class="input-text"
                                rows="4"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.notes" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.notes }}
                            </div>
                        </div>
                        <div class="input-container md:col-span-2">
                            <label for="res-create-doc" class="input-label">Link de documentação</label>
                            <input
                                id="res-create-doc"
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

                <div class="group-form mb-2 flex justify-end mt-6">
                    <Link type="button" class="btn-submit mr-2 bg-gray-700" :href="route('resources.index')">
                        Cancelar
                    </Link>
                    <button type="submit" class="btn-submit" :disabled="form.processing">Salvar</button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import SelectResourceCategory from '@/Components/SelectResourceCategory.vue';
import SelectDepartment from '@/Components/SelectDepartment.vue';
import { formatDocumentationUrl, normalizeDocumentationLinkForSubmit } from '@/helpers/masks';

const page = usePage();

defineProps({
    categorySelectOptions: {
        type: Array,
        default: () => [],
    },
    departmentSelectOptions: {
        type: Array,
        default: () => [],
    },
});

const pageTitle = 'Recursos';
const pageType = 'Cadastros';

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

const onDocumentationLinkInput = (event) => {
    form.documentation_link = formatDocumentationUrl(event.target.value);
};

const submit = () => {
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
        .post(route('resources.store'));
};
</script>
