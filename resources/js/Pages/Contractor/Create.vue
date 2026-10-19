<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-address-book" class="mr-2" />
                    Novo contratante
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('contractors.index')"
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
                        <div class="input-container md:col-span-2">
                            <label for="ct-name" class="input-label">Nome</label>
                            <input
                                id="ct-name"
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
                        <div class="input-container md:col-span-2">
                            <CustomerAutocomplete
                                v-model="form.customer_id"
                                input-id="ct-customer"
                                label="Cliente"
                                :options="customers"
                                :error-text="form.errors.customer_id"
                            />
                        </div>
                        <div class="input-container">
                            <label for="ct-phone" class="input-label">Telefone</label>
                            <input
                                id="ct-phone"
                                v-model="form.phone"
                                type="text"
                                name="phone"
                                class="input-text"
                                maxlength="20"
                                inputmode="numeric"
                                autocomplete="off"
                                @input="onPhoneInput"
                            />
                            <div v-if="form.errors.phone" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.phone }}
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="ct-email" class="input-label">E-mail</label>
                            <input
                                id="ct-email"
                                v-model="form.email"
                                type="email"
                                name="email"
                                class="input-text"
                                maxlength="120"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.email" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.email }}
                            </div>
                        </div>
                        <div class="input-container md:col-span-2">
                            <label for="ct-comm" class="input-label">Cuidados de comunicação</label>
                            <textarea
                                id="ct-comm"
                                v-model="form.communication_care"
                                name="communication_care"
                                class="input-text"
                                rows="4"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.communication_care" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.communication_care }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="group-form mb-2 flex justify-end mt-6">
                    <Link type="button" class="btn-submit mr-2 bg-gray-700" :href="route('contractors.index')">
                        Cancelar
                    </Link>
                    <button type="submit" class="btn-submit" :disabled="form.processing">Salvar</button>
                </div>
            </form>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import CustomerAutocomplete from '@/Components/CustomerAutocomplete.vue';
import { formatCellphone } from '@/helpers/masks';

const page = usePage();

const pageTitle = 'Contratantes';
const pageType = 'Cadastros';

const customers = computed(() => page.props.customers ?? []);

const form = useForm({
    name: '',
    customer_id: '',
    phone: '',
    email: '',
    communication_care: '',
});

const onPhoneInput = (event) => {
    form.phone = formatCellphone(event.target.value);
};

const submit = () => {
    form.post(route('contractors.store'));
};
</script>
