<template>
  <MainLayout>
    <div class="bg-white rounded-lg shadow p-6">
      <h2 class="text-3xl font-bold mb-4">{{ title }}</h2>

      <p class="text-gray-600 mb-6">
        Esta é uma página de exemplo usando Inertia.js com Laravel e Vue 3.
      </p>

      <Link href="/" class="inline-block px-4 py-2 bg-blue-500 text-white rounded">
        Voltar
      </Link>
    </div>
  </MainLayout>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'

const { props } = usePage()
const title = props.title
</script>
