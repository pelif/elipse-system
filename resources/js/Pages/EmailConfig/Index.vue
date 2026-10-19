<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />
        <Grid :emailConfigs="emailConfigs" :departments="departments" />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const { props } = usePage();

const pageTitle = 'Config. de E-mail';
const pageType = 'Configurações';

const emailConfigs = computed(() => props.emailConfigs);
const departments = computed(() => props.departments ?? []);
</script>

