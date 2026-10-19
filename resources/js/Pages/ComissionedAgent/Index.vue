<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />
        <Grid :comissioned-agents="comissionedAgents" :can-manage-comissioned-agents="canManageComissionedAgents" />
    </MainLayout>
</template>

<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const { props } = usePage();

const pageTitle = 'Comissionados';
const pageType = 'Cadastros';

const comissionedAgents = computed(() => props.comissionedAgents);
const canManageComissionedAgents = computed(() => props.canManageComissionedAgents === true);
</script>
