<template>
  <MainLayout>
    <Header :pageTitle="pageTitle" :pageType="pageType" />
    <Dashboard />
  </MainLayout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import Header from '@/Components/Header.vue'
import Dashboard from '@/Components/Dashboard.vue'

// Recebe props do controller
const { props } = usePage()

const pageTitle = 'Dashboard - Visão Geral'
const pageType = 'Painel Admin'

</script>
