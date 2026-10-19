<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />
        <Grid :service-units="serviceUnits" :can-manage-service-units="canManageServiceUnits" />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const page = usePage();

const { props } = usePage();

const pageTitle = 'Unidades de Serviço';
const pageType = 'Cadastros';

const serviceUnits = computed(() => props.serviceUnits);
const canManageServiceUnits = computed(() => props.canManageServiceUnits === true);
</script>
