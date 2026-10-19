<template>
    <MainLayout>
        <Header :page-title="pageTitle" :page-type="pageType" />
        <Grid
            :resources="resources"
            :can-manage-resources="canManageResources"
            :category-select-options="categorySelectOptions"
            :department-select-options="departmentSelectOptions"
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

const pageTitle = 'Recursos';
const pageType = 'Cadastros';

const resources = computed(() => props.resources);
const canManageResources = computed(() => props.canManageResources === true);
const categorySelectOptions = computed(() => props.categorySelectOptions ?? []);
const departmentSelectOptions = computed(() => props.departmentSelectOptions ?? []);
</script>
