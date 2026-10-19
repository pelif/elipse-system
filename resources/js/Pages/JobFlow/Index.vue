<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />
        <Grid :statuses="statuses" />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const { props } = usePage();

const pageTitle = 'Fluxo de Jobs';
const pageType = 'Configurações';

const statuses = computed(() => props.statuses);
</script>

