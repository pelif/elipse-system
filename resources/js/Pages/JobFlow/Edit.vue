<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-video" class="mr-2" />
                    Editar Status de Job
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('job-flow.index')"
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
                            <label for="status" class="input-label">Status</label>
                            <input
                                id="status"
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
                            <label for="observacoes" class="input-label">Observações</label>
                            <textarea
                                id="observacoes"
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

                    <div class="group-form"></div>

                    <div class="group-form">
                        <div class="group-form mb-2 flex justify-end">
                            <Link type="button" class="btn-submit mr-2 bg-gray-700" :href="route('job-flow.index')">
                                Cancelar
                            </Link>
                            <button type="submit" class="btn-submit" :disabled="form.processing">
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
    status: {
        type: Object,
        required: true,
    },
});

const pageTitle = 'Fluxo de Jobs';
const pageType = 'Configurações';

const form = useForm({
    status: props.status.status ?? '',
    observacoes: props.status.observacoes ?? '',
});

const submit = () => {
    form.put(route('job-flow.update', props.status.id));
};
</script>
