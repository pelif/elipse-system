<template>
    <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center gap-4">
                <h4 class="font-bold text-gray-700 text-2xl">
                    <font-awesome-icon icon="fa-solid fa-shoe-prints" class="mr-2" />
                    {{ pageTitle }}
                </h4>

                <div class="text-sm text-gray-500">
                    <span v-if="isLoading">Carregando…</span>
                    <span v-else>{{ items.length }} arquivo(s)</span>
                </div>
            </div>

            <div v-if="errorMessage" class="p-6">
                <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                    {{ errorMessage }}
                </div>
            </div>

            <div v-else class="p-6">
                <div class="overflow-hidden rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Arquivo
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Tamanho
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Modificado em
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            <tr v-if="!isLoading && items.length === 0">
                                <td class="px-4 py-6 text-sm text-gray-500" colspan="4">
                                    Nenhum arquivo de log encontrado.
                                </td>
                            </tr>

                            <tr v-for="log in items" :key="log.name">
                                <td class="px-4 py-4 text-sm font-semibold text-gray-800">
                                    {{ log.name }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ formatBytes(log.size) }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ formatDateTime(log.mtime) }}
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <Link
                                        class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-blue-500 cursor-pointer transition-all duration-300 ease-in-out"
                                        :href="route('config.logs.show', log.name)"
                                    >
                                        <font-awesome-icon icon="fa-solid fa-eye" />
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();

const pageTitle = 'Logs do Sistema';


const isLoading = ref(false);
const errorMessage = ref('');
const fetchedLogs = ref([]);

const props = defineProps({
    logs: {
        type: Array,
        default: null,
    },
});

const items = computed(() => {
    const fromProps = props.logs ?? page.props.logs ?? null;
    const data = fromProps ?? fetchedLogs.value ?? [];

    return (Array.isArray(data) ? data : []).map((x) => ({
        name: x.name ?? x.file ?? x.filename ?? '',
        size: Number(x.size ?? x.bytes ?? 0),
        mtime: x.mtime ?? x.modified_at ?? x.modifiedAt ?? null,
    })).filter((x) => !!x.name);
});

function formatBytes(bytes) {
    const value = Number(bytes ?? 0);
    if (!Number.isFinite(value) || value <= 0) return '0 B';
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.min(Math.floor(Math.log(value) / Math.log(1024)), units.length - 1);
    const sized = value / (1024 ** i);
    return `${sized.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
}

function formatDateTime(input) {
    if (!input) return '-';

    const date = typeof input === 'number'
        ? new Date(input * 1000)
        : new Date(input);

    if (Number.isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('pt-BR', { dateStyle: 'short', timeStyle: 'medium' }).format(date);
}

async function loadFromApi() {
    isLoading.value = true;
    errorMessage.value = '';

    try {
        const response = await window.axios.get(route('config.logs.index'));
        fetchedLogs.value = response?.data?.data ?? response?.data ?? [];
    } catch (e) {
        errorMessage.value = 'Não foi possível carregar a lista de logs.';
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    if (items.value.length === 0) {
        loadFromApi();
    }
});
</script>
