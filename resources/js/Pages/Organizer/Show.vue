<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-2xl">Organizador</h4>
                <Link
                    class="text-base font-semibold text-gray-700 hover:opacity-80"
                    :href="route('organizers.index')"
                >
                    Voltar
                </Link>
            </div>
            <div class="p-6">
                <ShowContent :organizer="organizer" />
            </div>
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

const pageTitle = 'Organizadores';
const pageType = 'Cadastros';

const organizer = computed(() => props.organizer);
</script>
