<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />
        <Grid :logs="logs" />
    </MainLayout>
</template>

<script setup>
// import { computed, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Grid from './Grid.vue';

const props = defineProps({
    logs: {
        type: Array,
        default: null,
    },
});

const pageTitle = 'Logs do Sistema';
const pageType = 'Configurações';

const page = usePage();

// const items = computed(() => {
//     const fromProps = props.logs ?? page.props.logs ?? null;
//     const data = fromProps ?? fetchedLogs.value ?? [];

//     return (Array.isArray(data) ? data : []).map((x) => ({
//         name: x.name ?? x.file ?? x.filename ?? '',
//         size: Number(x.size ?? x.bytes ?? 0),
//         mtime: x.mtime ?? x.modified_at ?? x.modifiedAt ?? null,
//     })).filter((x) => !!x.name);
// });

// function formatBytes(bytes) {
//     const value = Number(bytes ?? 0);
//     if (!Number.isFinite(value) || value <= 0) return '0 B';
//     const units = ['B', 'KB', 'MB', 'GB', 'TB'];
//     const i = Math.min(Math.floor(Math.log(value) / Math.log(1024)), units.length - 1);
//     const sized = value / (1024 ** i);
//     return `${sized.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
// }

// function formatDateTime(input) {
//     if (!input) return '-';

//     const date = typeof input === 'number'
//         ? new Date(input * 1000)
//         : new Date(input);

//     if (Number.isNaN(date.getTime())) return '-';
//     return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'medium' }).format(date);
// }

// async function loadFromApi() {
//     isLoading.value = true;
//     errorMessage.value = '';

//     try {
//         const response = await window.axios.get(route('config.logs.index'));
//         fetchedLogs.value = response?.data?.data ?? response?.data ?? [];
//     } catch (e) {
//         errorMessage.value = 'Não foi possível carregar a lista de logs.';
//     } finally {
//         isLoading.value = false;
//     }
// }

// onMounted(() => {
//     if (items.value.length === 0) {
//         loadFromApi();
//     }
// });
</script>
