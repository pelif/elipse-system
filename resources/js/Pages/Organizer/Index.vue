<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />
        <Grid
            :organizers="organizers"
            :customers="customers"
            :can-manage-organizers="canManageOrganizers"
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

const pageTitle = 'Organizadores';
const pageType = 'Cadastros';

const organizers = computed(() => props.organizers);
const customers = computed(() => props.customers ?? []);
const canManageOrganizers = computed(() => props.canManageOrganizers === true);
</script>
