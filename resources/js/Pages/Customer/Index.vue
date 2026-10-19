<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />
        <Grid :customers="customers" :can-manage-customers="canManageCustomers" />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const { props } = usePage();

const pageTitle = 'Clientes';
const pageType = 'Cadastros';

const customers = computed(() => props.customers);
const canManageCustomers = computed(() => props.canManageCustomers === true);
</script>
