<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-video" class="mr-2" />
                    Status de Job
                </h4>

                <div class="flex items-center gap-4">
                    <Link
                        class="text-base font-semibold text-orange-600 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                        title="Editar em página"
                        :href="route('job-flow.edit', status.id)"
                    >
                        Editar
                    </Link>
                    <Link
                        class="text-base font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                        title="Voltar"
                        :href="route('job-flow.index')"
                    >
                        Voltar
                    </Link>
                </div>
            </div>

            <ShowContent :status="status" />
        </div>
    </MainLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import ShowContent from './ShowContent.vue';

defineProps({
    status: {
        type: Object,
        default: () => ({}),
    },
});

const pageTitle = 'Fluxo de Jobs';
const pageType = 'Configurações';
</script>

