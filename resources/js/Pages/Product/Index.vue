<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />
        <Grid :products="products" :can-manage-products="canManageProducts" />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const { props } = usePage();

const pageTitle = 'Produtos';
const pageType = 'Cadastros';

const products = computed(() => props.products);
const canManageProducts = computed(() => props.canManageProducts === true);
</script>
