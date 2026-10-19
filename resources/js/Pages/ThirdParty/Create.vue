<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />

        <div
            class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
        >
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-address-book" class="mr-2" />
                    Novo terceiro
                </h4>

                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('third-parties.index')"
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
                            <label for="tp-name" class="input-label">Nome</label>
                            <input
                                id="tp-name"
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
                            <label for="tp-alias" class="input-label">Apelido</label>
                            <input
                                id="tp-alias"
                                v-model="form.alias"
                                type="text"
                                name="alias"
                                class="input-text"
                                maxlength="255"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.alias" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.alias }}
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="tp-phone" class="input-label">Telefone</label>
                            <input
                                id="tp-phone"
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
                            <label for="tp-phone-alt" class="input-label">Telefone alternativo</label>
                            <input
                                id="tp-phone-alt"
                                v-model="form.phone_alternative"
                                type="text"
                                name="phone_alternative"
                                class="input-text"
                                maxlength="20"
                                inputmode="numeric"
                                autocomplete="off"
                                @input="onPhoneAltInput"
                            />
                            <div v-if="form.errors.phone_alternative" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.phone_alternative }}
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="tp-email" class="input-label">E-mail</label>
                            <input
                                id="tp-email"
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
                        <div class="input-container">
                            <label for="tp-email-alt" class="input-label">E-mail alternativo</label>
                            <input
                                id="tp-email-alt"
                                v-model="form.email_alternative"
                                type="email"
                                name="email_alternative"
                                class="input-text"
                                maxlength="120"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.email_alternative" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.email_alternative }}
                            </div>
                        </div>
                        <div class="input-container md:col-span-2">
                            <label for="tp-indication" class="input-label">Indicação</label>
                            <input
                                id="tp-indication"
                                v-model="form.indication"
                                type="text"
                                name="indication"
                                class="input-text"
                                maxlength="120"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.indication" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.indication }}
                            </div>
                        </div>
                        <div class="input-container md:col-span-2">
                            <label for="tp-observation" class="input-label">Observações</label>
                            <textarea
                                id="tp-observation"
                                v-model="form.observation"
                                name="observation"
                                class="input-text"
                                rows="4"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.observation" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.observation }}
                            </div>
                        </div>
                        <div class="input-container md:col-span-2">
                            <label for="tp-company" class="input-label">Razão social</label>
                            <input
                                id="tp-company"
                                v-model="form.company_name"
                                type="text"
                                name="company_name"
                                class="input-text"
                                maxlength="150"
                                autocomplete="off"
                            />
                            <div v-if="form.errors.company_name" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.company_name }}
                            </div>
                        </div>
                        <div class="input-container">
                            <label for="tp-document" class="input-label">CNPJ</label>
                            <input
                                id="tp-document"
                                v-model="form.document"
                                type="text"
                                name="document"
                                class="input-text"
                                maxlength="18"
                                inputmode="numeric"
                                autocomplete="off"
                                @input="onCnpjInput"
                            />
                            <div v-if="form.errors.document" class="input-error">
                                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                                {{ form.errors.document }}
                            </div>
                        </div>
                        <div class="input-container md:col-span-2">
                            <MultiSelectChips
                                input-id="tp-services"
                                label="Especialidades"
                                v-model="form.service_ids"
                                :options="serviceSelectOptions"
                                :error="form.errors.service_ids"
                                placeholder="Selecione os serviços"
                            />
                        </div>
                        <div class="input-container flex items-center gap-2 mt-2">
                            <input id="tp-active" v-model="form.active" type="checkbox" class="rounded" name="active" />
                            <label for="tp-active" class="input-label mb-0">Ativo</label>
                            <div v-if="form.errors.active" class="input-error">{{ form.errors.active }}</div>
                        </div>
                    </div>
                </div>

                <div class="group-form mb-2 flex justify-end mt-6">
                    <Link type="button" class="btn-submit mr-2 bg-gray-700" :href="route('third-parties.index')">
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
import MultiSelectChips from '@/Components/MultiSelectChips.vue';
import { formatCnpj, formatCellphone } from '@/helpers/masks';

const page = usePage();

defineProps({
    serviceSelectOptions: {
        type: Array,
        default: () => [],
    },
});

const pageTitle = 'Terceiros';
const pageType = 'Cadastros';

const form = useForm({
    name: '',
    alias: '',
    phone: '',
    phone_alternative: '',
    email: '',
    email_alternative: '',
    indication: '',
    observation: '',
    company_name: '',
    document: '',
    active: true,
    service_ids: [],
});

const onCnpjInput = (event) => {
    form.document = formatCnpj(event.target.value);
};

const onPhoneInput = (event) => {
    form.phone = formatCellphone(event.target.value);
};

const onPhoneAltInput = (event) => {
    form.phone_alternative = formatCellphone(event.target.value);
};

const submit = () => {
    form
        .transform((data) => {
            const docDigits = String(data.document ?? '').replace(/\D/g, '');
            return {
                ...data,
                document: docDigits,
            };
        })
        .post(route('third-parties.store'));
};
</script>
