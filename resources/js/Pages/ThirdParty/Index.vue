<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />
        <Grid
            :third-parties="thirdParties"
            :can-manage-third-parties="canManageThirdParties"
            :service-select-options="serviceSelectOptions"
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

const pageTitle = 'Terceiros';
const pageType = 'Cadastros';

const thirdParties = computed(() => props.thirdParties);
const canManageThirdParties = computed(() => props.canManageThirdParties === true);
const serviceSelectOptions = computed(() => {
    if (!Array.isArray(props.serviceSelectOptions)) {
        return [];
    }
    return props.serviceSelectOptions;
});
</script>
