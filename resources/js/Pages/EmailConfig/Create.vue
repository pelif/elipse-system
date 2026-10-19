<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-envelope" class="mr-2" />
                    Nova Configuração de E-mail
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('email-config.index')"
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
                            <Link
                                type="button"
                                class="btn-submit mr-2 bg-gray-700"
                                :href="route('email-config.index')">
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

const props = defineProps({
    departments: {
        type: Array,
        required: true,
    },
});

const pageTitle = 'Config. de E-mail';
const pageType = 'Configurações';

const form = useForm({
    address: '',
    department_id: '',
});

const submit = () => {
    form.post(route('email-config.store'));
};
</script>

