<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div
            class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
        >
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-box" class="mr-2" />
                    Adicionar produto
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('products.index')"
                >
                    Voltar
                </Link>
            </div>

            <form class="p-6" @submit.prevent="submit">
                <div v-if="page.flash.message" class="success-message">
                    <font-awesome-icon icon="fa-solid fa-check" class="mr-2 text-2xl" />
                    {{ page.flash.message }}
                </div>
                <div v-if="page.flash.error" class="error-message">
                    <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                    {{ page.flash.error }}
                </div>

                <div class="container-form">
                    <div class="group-form">
                        <div class="input-container">
                            <label for="product-name" class="input-label">Produto</label>
                            <input
                                id="product-name"
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

                        <div class="input-container mt-4">
                            <label for="product-description" class="input-label">Descrição</label>
                            <textarea
                                id="product-description"
                                v-model="form.description"
                                name="description"
                                class="input-text"
                                maxlength="500"
                                rows="4"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.description" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.description }}
                            </div>
                        </div>
                    </div>

                    <div class="group-form"></div>

                    <div class="group-form mb-2 flex justify-end mt-6">
                        <Link
                            type="button"
                            class="btn-submit mr-2 bg-gray-700"
                            :href="route('products.index')"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="btn-submit"
                            :disabled="form.processing">
                            Salvar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const page = usePage();

const pageTitle = 'Produtos';
const pageType = 'Cadastros';

const form = useForm({
    name: '',
    description: '',
});

const submit = () => {
    form.post(route('products.store'));
};
</script>
