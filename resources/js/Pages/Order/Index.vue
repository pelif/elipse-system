<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />
        <Grid :orders="orders" />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const page = usePage();

const pageTitle = 'Consultar OS';
const pageType = 'Fluxo de Trabalho';

const orders = computed(() => page.props.orders);
</script>
