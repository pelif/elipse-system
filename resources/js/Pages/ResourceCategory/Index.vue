<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />
        <Grid
            :resource-categories="resourceCategories"
            :can-manage-resource-categories="canManageResourceCategories"
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

const pageTitle = 'Categorias';
const pageType = 'Cadastros';

const resourceCategories = computed(() => props.resourceCategories);
const canManageResourceCategories = computed(() => props.canManageResourceCategories === true);
</script>
