<template>
    <div class="w-auto mx-4 my-6 bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-tools" class="mr-2" />
                Consultar OS
            </h4>
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
            :filter-fields="orderFilterFields"
            :sort-fields="orderSortFields"
            :show-search="false"
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
        />

        <div v-if="rows.length === 0" class="p-6 text-center">
            Nenhuma OS cadastrada.
        </div>

        <div v-else class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                    <tr>
                        <th class="px-6 py-4">OS/Título</th>
                        <th class="px-6 py-4">Job/Cliente</th>
                        <th class="px-6 py-4">Situação</th>
                        <th class="px-6 py-4">Agenda</th>
                        <th class="px-6 py-4">Local/Tipo</th>
                        <th class="px-6 py-4">Organizador/Contratante</th>
                        <th class="px-6 py-4">Tarefas</th>
                        <th class="px-6 py-4">Criação</th>
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
                            <p class="font-bold text-gray-800">{{ displayText(row.osCode) }}</p>
                            <p class="text-sm text-gray-400">{{ displayText(row.eventTitle) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-bold text-gray-800">{{ displayText(row.jobCode) }}</p>
                            <p class="text-sm text-gray-400">{{ displayText(row.customerLegalName) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex px-3 py-1 rounded-full text-xs font-bold"
                                :class="statusBadgeClass(row.statusLabel)"
                            >
                                {{ displayText(row.statusLabel) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <p class="text-gray-700">{{ formatEventDate(row.startTime) }}</p>
                            <p class="text-gray-500">{{ formatSchedule(row) }}</p>
                            <p class="text-gray-400">{{ displayText(row.durationLabel) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <p class="text-gray-700">{{ displayText(row.location) }}</p>
                            <p class="text-gray-400">{{ displayText(row.osType) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <p class="text-gray-700">{{ displayText(row.organizerName) }}</p>
                            <p class="text-gray-400">{{ displayText(row.contractorName) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm">0/0 concluídas</td>
                        <td class="px-6 py-4 text-sm">
                            <p class="text-gray-700">{{ formatDate(row.createdAt) }}</p>
                            <p class="text-gray-400">{{ formatDate(row.updatedAt) }}</p>
                        </td>
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
            v-if="orders?.meta && orders.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ orders.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ orders.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in orders.meta.links" :key="index">
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
            <Link
                :href="route('orders.edit', { id: openRow.id })"
                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50"
            >
                Abrir OS
            </Link>
            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50" @click="closeMenu">
                Tarefas
            </button>
            <button type="button" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50" @click="closeMenu">
                Job
            </button>
        </div>
    </Teleport>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import FilterBar from '@/Components/FilterBar.vue';

const FILTER_FIELD_PAIRS = [
    ['order_code', 'osCode'],
    ['job_code', 'jobCode'],
    ['event_title', 'eventTitle'],
    ['customer', 'customer'],
    ['organizer', 'organizer'],
];

const orderFilterFields = [
    { key: 'order_code', label: 'Código OS' },
    { key: 'job_code', label: 'Código Job' },
    { key: 'event_title', label: 'Título OS' },
    { key: 'customer', label: 'Cliente' },
    { key: 'organizer', label: 'Organizador' },
];

const orderSortFields = [
    { key: 'order_code', label: 'Código OS' },
    { key: 'event_title', label: 'Título' },
    { key: 'start_time', label: 'Agenda' },
    { key: 'created_at', label: 'Criação' },
    { key: 'updated_at', label: 'Atualização' },
];

const page = usePage();

const props = defineProps({
    orders: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
});

const rows = computed(() => {
    if (props.orders && Array.isArray(props.orders.data)) {
        return props.orders.data;
    }

    return [];
});

const initialFilters = computed(() => props.orders?.filters ?? {});

function inferFilterPair(source) {
    if (!source || typeof source !== 'object') {
        return { filterField: 'order_code', filterValue: '' };
    }
    for (const [snake, camel] of FILTER_FIELD_PAIRS) {
        const value = source[camel];
        if (value !== undefined && value !== null && String(value).trim() !== '') {
            return { filterField: snake, filterValue: String(value) };
        }
    }

    return { filterField: 'order_code', filterValue: '' };
}

const filters = reactive({
    filterField: 'order_code',
    filterValue: '',
    search: '',
    sortField: 'created_at',
    sortDirection: 'desc',
});

watch(
    initialFilters,
    (next) => {
        const inferred = inferFilterPair(next);
        filters.filterField = inferred.filterField;
        filters.filterValue = inferred.filterValue;
        filters.sortField = next.sortField ?? 'created_at';
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

const parseDateTime = (value) => {
    if (!value) {
        return null;
    }
    const normalized = String(value).trim().replace(' ', 'T');
    const date = new Date(normalized);
    if (Number.isNaN(date.getTime())) {
        return null;
    }

    return date;
};

const formatEventDate = (value) => {
    const date = parseDateTime(value);
    if (!date) {
        return '—';
    }

    return date.toLocaleDateString('pt-BR');
};

const formatEventTime = (value) => {
    const date = parseDateTime(value);
    if (!date) {
        return '—';
    }

    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
};

const formatSchedule = (row) => {
    const start = formatEventTime(row.startTime);
    const end = formatEventTime(row.endTime);
    if (start === '—' && end === '—') {
        return '—';
    }
    if (start === '—') {
        return end;
    }
    if (end === '—') {
        return start;
    }

    return start + ' – ' + end;
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
    if (name === 'Não Agendado') {
        return 'bg-slate-100 text-slate-700';
    }
    if (name === 'Provisório') {
        return 'bg-amber-100 text-amber-800';
    }
    if (name === 'Agendado') {
        return 'bg-blue-100 text-blue-800';
    }
    if (name === 'Finalizado') {
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

    router.get(route('orders.index'), params, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'order_code';
    filters.filterValue = '';
    filters.sortField = 'created_at';
    filters.sortDirection = 'desc';
    applyFilters();
};

const openRow = ref(null);
const menuStyle = ref({ top: '0px', left: '0px' });

const openActions = (row, event) => {
    if (openRow.value && openRow.value.id === row.id) {
        openRow.value = null;
        return;
    }
    const rect = event.currentTarget.getBoundingClientRect();
    let top = rect.bottom + 4;
    const menuHeight = 140;
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
