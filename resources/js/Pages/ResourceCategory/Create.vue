<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-tags" class="mr-2" />
                    Nova categoria
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('resource-categories.index')"
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
                    <div class="group-form grid grid-cols-1 gap-4">
                        <div class="input-container">
                            <label for="cat-name" class="input-label">Nome da Categoria</label>
                            <input
                                id="cat-name"
                                v-model="form.name"
                                type="text"
                                name="name"
                                class="input-text"
                                maxlength="120"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.name" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.name }}
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="cat-desc" class="input-label">Descrição</label>
                            <textarea
                                id="cat-desc"
                                v-model="form.description"
                                name="description"
                                class="input-text"
                                rows="4"
                                maxlength="255"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.description" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.description }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group-form mb-2 flex justify-end mt-6">
                    <button
                        type="button"
                        class="btn-submit mr-2 bg-gray-700"
                        @click="goCancel"
                    >
                        Cancelar
                    </button>
                    <button type="submit" class="btn-submit" :disabled="form.processing">Salvar</button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const page = usePage();

const pageTitle = 'Categorias';
const pageType = 'Cadastros';

const form = useForm({
    name: '',
    description: '',
});

const goCancel = () => {
    router.get(route('resource-categories.index'));
};

const submit = () => {
    form.post(route('resource-categories.store'));
};
</script>
