<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div
            class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
        >
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-award" class="mr-2" />
                    Adicionar comissionado
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('comissioned-agents.index')"
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
                            <label for="comissioned-name" class="input-label">Nome</label>
                            <input
                                id="comissioned-name"
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
                    </div>

                    <div class="group-form">
                        <div class="input-container">
                            <label for="comissioned-default-comission" class="input-label">Comissão Padrão (%)</label>
                            <input
                                id="comissioned-default-comission"
                                v-model="form.default_comission"
                                type="number"
                                name="default_comission"
                                class="input-text"
                                min="0"
                                max="100"
                                step="0.01"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.default_comission" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.default_comission }}
                            </div>
                        </div>
                    </div>

                    <div class="group-form">
                        <div class="input-container">
                            <label for="comissioned-payment-days" class="input-label">Dias de Pagamento</label>
                            <input
                                id="comissioned-payment-days"
                                v-model="form.payment_days"
                                type="number"
                                name="payment_days"
                                class="input-text"
                                min="0"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.payment_days" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.payment_days }}
                            </div>
                        </div>
                    </div>

                    <div class="group-form">
                        <div class="input-container">
                            <label for="comissioned-observations" class="input-label">Observações</label>
                            <textarea
                                id="comissioned-observations"
                                v-model="form.observations"
                                name="observations"
                                class="input-text"
                                rows="4"
                            ></textarea>
                            <div v-if="form.errors.observations" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.observations }}
                            </div>
                        </div>
                    </div>

                    <div class="group-form mb-2 flex justify-end mt-6">
                        <Link
                            type="button"
                            class="btn-submit mr-2 bg-gray-700"
                            :href="route('comissioned-agents.index')"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            class="btn-submit"
                            :disabled="form.processing"
                        >
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

const pageTitle = 'Comissionados';
const pageType = 'Cadastros';

defineProps({
    canManageComissionedAgents: {
        type: Boolean,
        default: false,
    },
});

const form = useForm({
    name: '',
    default_comission: '',
    payment_days: '',
    observations: '',
});

const submit = () => {
    form.post(route('comissioned-agents.store'));
};
</script>
