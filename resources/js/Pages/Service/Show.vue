<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">Serviço</h4>
                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80"
                    :href="route('services.index')"
                >
                    Voltar
                </Link>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Serviço</div>
                    <div class="text-lg font-semibold text-gray-900">{{ service?.service }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Unidade</div>
                    <div class="text-gray-700">{{ service?.unity || '-' }}</div>
                </div>
                <div>
                    <div class="text-xs uppercase text-gray-500 font-bold">Descrição</div>
                    <div class="text-gray-700">{{ service?.description || '-' }}</div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const { props } = usePage();

const pageTitle = 'Serviços';
const pageType = 'Cadastros';

const service = computed(() => props.service);
</script>
