<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />
        <Grid
            :services="services"
            :can-manage-services="canManageServices"
            :service-unity-select-options="serviceUnitySelectOptions"
        />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const { props } = usePage();

const pageTitle = 'Serviços';
const pageType = 'Cadastros';

const services = computed(() => props.services);
const canManageServices = computed(() => props.canManageServices === true);
const serviceUnitySelectOptions = computed(() => {
    if (Array.isArray(props.serviceUnitySelectOptions)) {
        return props.serviceUnitySelectOptions;
    }
    return [];
});
</script>
