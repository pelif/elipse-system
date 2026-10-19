<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div class="w-[98%] mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-user" class="mr-2" />
                    Minha Conta
                </h4>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Nome</div>
                    <div class="text-lg font-semibold text-gray-900">{{ user?.name ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">E-mail</div>
                    <div class="text-lg font-semibold text-gray-900">{{ user?.email ?? '-' }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Perfil</div>
                    <div class="text-lg font-semibold text-gray-900">{{ user?.roleFormatted ?? '-' }}</div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const { props } = usePage();

const pageTitle = 'Minha Conta';
const pageType = 'Perfil';

const user = computed(() => props.user);
</script>
