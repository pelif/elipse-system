<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />
        <Grid
            :contractors="contractors"
            :customers="customers"
            :can-manage-contractors="canManageContractors"
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

const pageTitle = 'Contratantes';
const pageType = 'Cadastros';

const contractors = computed(() => props.contractors);
const customers = computed(() => props.customers ?? []);
const canManageContractors = computed(() => props.canManageContractors === true);
</script>
