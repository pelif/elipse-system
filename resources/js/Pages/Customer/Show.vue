<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div class="w-auto mx-4 my-6 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-user" class="mr-2" />
                    Cliente
                </h4>
                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Voltar"
                    :href="route('customers.index')"
                >
                    Voltar à lista
                </Link>
            </div>

            <ShowContent :customer="customer" />
        </div>
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import ShowContent from './ShowContent.vue';

const { props } = usePage();

const pageTitle = 'Clientes';
const pageType = 'Cadastros';

const customer = computed(() => props.customer ?? {});
</script>
