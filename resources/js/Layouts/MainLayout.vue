<template>
  <div class="flex h-screen overflow-hidden">

    <!-- Aside Menu -->
    <Aside />

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import Aside from '../Components/Aside.vue'

// Acessa dados compartilhados via middleware
const { props } = usePage()
const appName = props.appName
</script>

<style scoped>
/* Seus estilos aqui */
</style>
