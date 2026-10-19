<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />

        <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center gap-4">
                <div class="min-w-0">
                    <h4 class="font-bold text-gray-700 text-2xl truncate">
                        <Link :href="route('config.logs.index')">
                            <font-awesome-icon icon="fa-solid fa-arrow-left" class="mr-2" />
                        </Link>
                        {{ fileName || 'Log do Sistema' }}
                    </h4>
                    <p v-if="fileSizeBytes > 0 && !errorMessage" class="text-xs text-gray-500 mt-1">
                        Arquivo completo carregado. Tamanho: <strong>{{ formatBytes(fileSizeBytes) }}</strong>.
                    </p>
                    <p v-else-if="!errorMessage" class="text-xs text-gray-500 mt-1">
                        Nenhum conteúdo disponível para este arquivo.
                    </p>
                </div>

                <button
                    type="button"
                    class="text-sm font-semibold text-gray-700 hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    :disabled="isLoading"
                    @click="refresh"
                >
                    <font-awesome-icon icon="fa-solid fa-rotate-right" class="mr-2" />
                    Recarregar
                </button>
            </div>

            <div v-if="errorMessage" class="p-6">
                <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
                    {{ errorMessage }}
                </div>
            </div>

            <div class="p-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div class="text-xs text-gray-500">
                        <span v-if="isLoading">Carregando…</span>
                        <span v-else>Linhas: {{ contentLinesCount }}</span>
                    </div>
                </div>

                <pre
                    class="max-h-[70vh] overflow-auto bg-slate-950 text-slate-100 font-mono text-xs p-4 rounded-xl border border-slate-900 whitespace-pre-wrap"
                ><code>{{ content || 'Nenhum conteúdo para exibir.' }}</code></pre>
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Header from '@/Components/Header.vue';
import MainLayout from '@/Layouts/MainLayout.vue';

const props = defineProps({
    file: {
        type: String,
        default: '',
    },
    content: {
        type: String,
        default: '',
    },
    file_size_bytes: {
        type: Number,
        default: 0,
    },
    error_message: {
        type: String,
        default: '',
    },
});

const pageTitle = 'Logs do Sistema';
const pageType = 'Configurações';

const page = usePage();
const isLoading = ref(false);

const content = computed(() => props.content || page.props.content || '');
const errorMessage = computed(() => props.error_message || page.props.error_message || '');
const fileSizeBytes = computed(() => Number(props.file_size_bytes || page.props.file_size_bytes || 0) || 0);

const fileName = computed(() => {
    const fromProps = props.file || page.props.file || '';
    if (fromProps) {
        return fromProps;
    }

    const url = String(page.url || '');
    const last = url.split('?')[0].split('/').filter(Boolean).pop();

    return last ? decodeURIComponent(last) : '';
});

const contentLinesCount = computed(() => {
    if (!content.value) {
        return 0;
    }

    return content.value.split('\n').length;
});

function formatBytes(bytes) {
    const value = Number(bytes ?? 0);

    if (!Number.isFinite(value) || value <= 0) {
        return '0 B';
    }

    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.min(Math.floor(Math.log(value) / Math.log(1024)), units.length - 1);
    const sized = value / (1024 ** i);

    return `${sized.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
}

function refresh() {
    if (!fileName.value) {
        return;
    }

    isLoading.value = true;

    router.get(
        route('config.logs.show', fileName.value),
        {},
        {
            preserveScroll: true,
            preserveState: false,
            replace: true,
            onFinish: () => {
                isLoading.value = false;
            },
        }
    );
}
</script>
