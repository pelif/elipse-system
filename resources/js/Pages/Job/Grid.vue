<template>
    <div class="w-auto mx-4 my-6 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-video" class="mr-2" />
                Jobs
            </h4>
            <Link
                v-if="canCreateJobs"
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar job"
                :href="route('jobs.create')"
            >
                <font-awesome-icon icon="fa-solid fa-square-plus " />
            </Link>
        </div>

        <div v-if="page.flash.message" class="success-message">
            <font-awesome-icon icon="fa-solid fa-check" class="mr-2 text-2xl" />
            {{ page.flash.message }}
        </div>
        <div v-if="page.flash.error" class="error-message">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
            {{ page.flash.error }}
        </div>

        <filter-bar
            :filters="filters"
            :filter-fields="jobFilterFields"
            :sort-fields="jobSortFields"
            :show-search="false"
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
        />

        <div v-if="rows.length === 0" class="p-6 text-center">
            Nenhum job cadastrado.
        </div>

        <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                    <tr>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Job</th>
                        <th class="px-6 py-4">Contratante</th>
                        <th class="px-6 py-4">Produto</th>
                        <th class="px-6 py-4">OS</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Criação</th>
                        <th class="px-6 py-4">Criado por</th>
                        <th class="px-6 py-4 w-[1%] whitespace-nowrap">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="row in rows"
                        :key="row.id"
                        class="hover:bg-yellow-100 transition-colors text-gray-500"
                    >
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div
                                    class="w-8 h-8 rounded flex items-center justify-center text-white font-bold text-xs shrink-0"
                                    :class="randomBg()"
                                >
                                    {{ printInitials(row.customerName || '') }}
                                </div>
                                <span class="font-bold text-gray-700">{{ displayText(row.customerName) }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800">{{ displayText(row.jobCode) }}</p>
                            <p class="text-sm text-gray-400">{{ displayText(row.jobTitle) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ displayText(row.contractorName) }}</td>
                        <td class="px-6 py-4 text-sm">{{ displayText(row.productName) }}</td>
                        <td class="px-6 py-4 text-sm">{{ row.osQuantity }}</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex px-3 py-1 rounded-full text-xs font-bold"
                                :class="statusBadgeClass(row.statusName)"
                            >
                                {{ displayText(row.statusName) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <p class="text-gray-700">{{ formatDate(row.createdAt) }}</p>
                            <p class="text-gray-400">{{ formatDate(row.updatedAt) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ displayText(row.createdByName) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button
                                type="button"
                                class="text-xl text-gray-500 hover:text-gray-800 cursor-pointer px-2"
                                title="Ações"
                                @click="openActions(row, $event)"
                            >
                                <font-awesome-icon icon="fa-solid fa-ellipsis-vertical" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="jobs?.meta && jobs.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ jobs.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ jobs.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in jobs.meta.links" :key="index">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-4 py-2 text-sm rounded-lg border font-medium transition-all duration-200"
                        :class="paginationClass(link.active)"
                        preserve-scroll
                    >
                        <span v-html="link.label"></span>
                    </Link>
                    <span
                        v-else
                        v-html="link.label"
                        class="px-4 py-2 text-sm rounded-lg border font-medium transition-all duration-200 text-gray-400 bg-gray-50 border-gray-100 cursor-not-allowed shadow-sm"
                    ></span>
                </template>
            </div>
        </div>
    </div>

    <Teleport to="body">
        <div v-if="openRow" class="fixed inset-0 z-40" @click="closeMenu"></div>
        <div
            v-if="openRow"
            class="fixed z-50 w-60 rounded-lg border border-gray-200 bg-white shadow-lg py-1"
            :style="menuStyle"
            @click.stop
        >
            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50" @click="closeMenu">
                Gerir Detalhes
            </button>
            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50" @click="closeMenu">
                Orçamentos
            </button>
            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50" @click="closeMenu">
                Histórico
            </button>
            <p class="px-4 pt-3 pb-1 text-xs uppercase text-gray-400 font-bold">Atualizar etapa</p>
            <button
                v-for="status in otherStatuses"
                :key="status.id"
                type="button"
                class="block w-full text-left px-4 py-2 hover:bg-yellow-50"
                @click="closeMenu"
            >
                <span
                    class="inline-flex px-3 py-1 rounded-full text-xs font-bold"
                    :class="statusBadgeClass(status.name)"
                >
                    {{ status.name }}
                </span>
            </button>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import FilterBar from '@/Components/FilterBar.vue';
import { printInitials, randomBg } from '@/helpers/styles';

const FILTER_FIELD_PAIRS = [
    ['customer', 'customer'],
    ['job_code', 'jobCode'],
    ['job_title', 'jobTitle'],
    ['product', 'product'],
    ['status', 'status'],
    ['contractor', 'contractor'],
    ['created_at', 'createdAt'],
    ['updated_at', 'updatedAt'],
];

const jobFilterFields = [
    { key: 'customer', label: 'Cliente' },
    { key: 'job_code', label: 'Código' },
    { key: 'job_title', label: 'Título' },
    { key: 'product', label: 'Produto' },
    { key: 'status', label: 'Status' },
    { key: 'contractor', label: 'Contratante' },
    { key: 'created_at', label: 'Criação' },
    { key: 'updated_at', label: 'Atualização' },
];

const jobSortFields = [
    { key: 'job_code', label: 'Código' },
    { key: 'job_title', label: 'Título' },
    { key: 'os_quantity', label: 'OS' },
    { key: 'created_at', label: 'Criação' },
    { key: 'updated_at', label: 'Atualização' },
];

const page = usePage();

const props = defineProps({
    jobs: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    canCreateJobs: {
        type: Boolean,
        default: false,
    },
    jobStatuses: {
        type: Array,
        default: () => [],
    },
});

const rows = computed(() => {
    if (props.jobs && Array.isArray(props.jobs.data)) {
        return props.jobs.data;
    }

    return [];
});

const initialFilters = computed(() => props.jobs?.filters ?? {});

function inferFilterPair(source) {
    if (!source || typeof source !== 'object') {
        return { filterField: 'customer', filterValue: '' };
    }
    for (const [snake, camel] of FILTER_FIELD_PAIRS) {
        const value = source[camel];
        if (value !== undefined && value !== null && String(value).trim() !== '') {
            return { filterField: snake, filterValue: String(value) };
        }
    }

    return { filterField: 'customer', filterValue: '' };
}

const filters = reactive({
    filterField: 'customer',
    filterValue: '',
    search: '',
    sortField: 'updated_at',
    sortDirection: 'desc',
});

watch(
    initialFilters,
    (next) => {
        const inferred = inferFilterPair(next);
        filters.filterField = inferred.filterField;
        filters.filterValue = inferred.filterValue;
        filters.sortField = next.sortField ?? 'updated_at';
        filters.sortDirection = next.sortDirection ?? 'desc';
    },
    { immediate: true },
);

const displayText = (value) => {
    if (value === null || value === undefined) {
        return '—';
    }
    const text = String(value).trim();
    if (text === '') {
        return '—';
    }

    return text;
};

const formatDate = (iso) => {
    if (!iso) {
        return '—';
    }
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) {
        return '—';
    }

    return date.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
};

const statusBadgeClass = (name) => {
    if (name === 'Em análise') {
        return 'bg-amber-100 text-amber-800';
    }
    if (name === 'Orçar') {
        return 'bg-blue-100 text-blue-800';
    }
    if (name === 'Faturar') {
        return 'bg-indigo-100 text-indigo-800';
    }
    if (name === 'Concluído') {
        return 'bg-green-100 text-green-800';
    }
    if (name === 'Cancelado') {
        return 'bg-red-100 text-red-800';
    }

    return 'bg-gray-100 text-gray-600';
};

const paginationClass = (active) => {
    if (active) {
        return 'bg-linear-to-r from-blue-500 to-indigo-600 text-white border-transparent shadow-md hover:shadow-lg';
    }

    return 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 shadow-sm';
};

const applyFilters = (nextFilters) => {
    if (nextFilters && typeof nextFilters === 'object') {
        filters.filterField = nextFilters.filterField ?? filters.filterField;
        filters.filterValue = nextFilters.filterValue ?? filters.filterValue;
        filters.sortField = nextFilters.sortField ?? filters.sortField;
        filters.sortDirection = nextFilters.sortDirection ?? filters.sortDirection;
    }

    let rawFilterValue = '';
    if (typeof filters.filterValue === 'string') {
        rawFilterValue = filters.filterValue.trim();
    }

    const params = {
        page: 1,
        sortField: filters.sortField,
        sortDirection: filters.sortDirection,
    };

    if (rawFilterValue !== '') {
        params[filters.filterField] = rawFilterValue;
    }

    router.get(route('jobs.index'), params, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'customer';
    filters.filterValue = '';
    filters.sortField = 'updated_at';
    filters.sortDirection = 'desc';
    applyFilters();
};

const openRow = ref(null);
const menuStyle = ref({ top: '0px', left: '0px' });

const otherStatuses = computed(() => {
    if (!openRow.value) {
        return [];
    }
    const current = openRow.value.statusName;
    const list = [];
    for (let i = 0; i < props.jobStatuses.length; i++) {
        const status = props.jobStatuses[i];
        if (status.name !== current) {
            list.push(status);
        }
    }

    return list;
});

const openActions = (row, event) => {
    if (openRow.value && openRow.value.id === row.id) {
        openRow.value = null;
        return;
    }
    const rect = event.currentTarget.getBoundingClientRect();
    let top = rect.bottom + 4;
    const menuHeight = 280;
    if (top + menuHeight > window.innerHeight) {
        top = rect.top - menuHeight;
        if (top < 8) {
            top = 8;
        }
    }
    let left = rect.right - 240;
    if (left < 8) {
        left = 8;
    }
    menuStyle.value = {
        top: top + 'px',
        left: left + 'px',
    };
    openRow.value = row;
};

const closeMenu = () => {
    openRow.value = null;
};
</script>
