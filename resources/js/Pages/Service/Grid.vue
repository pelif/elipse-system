<template>
    <div
        class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
    >
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-gear" class="mr-2" />
                Serviços
            </h4>

            <div class="flex justify-items-end items-end gap-2">
                <button
                    class="self-start mt-1.5 text-2xl bg-gray-200 text-sm px-4 py-1 text-gray-500 border border-gray-500 rounded-sm cursor-pointer text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out"
                    title="Unidades de Serviço"
                    type="button"
                    @click="goToServiceUnity"
                >
                    <font-awesome-icon icon="fa-solid fa-cubes" class="mr-2" />
                    Unidades de Serviço
                </button>

                <button
                    v-if="canManageServices"
                    class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Adicionar serviço"
                    type="button"
                    @click="goToCreate"
                >
                    <font-awesome-icon icon="fa-solid fa-square-plus" />
                </button>
            </div>
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
            :filter-fields="serviceFilterFields"
            :search-fields="serviceSearchFields"
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
        />

        <div
            v-if="services?.data && services.data.length === 0"
            class="p-6 text-center"
        >
            Nenhum serviço cadastrado.
        </div>

        <table v-else class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4 w-[20%]">Serviço</th>
                    <th class="px-6 py-4 w-[10%]">Unidade</th>
                    <th class="px-6 py-4 w-[36%]">Descrição</th>
                    <th class="px-6 py-4 w-[10%]">Cadastro</th>
                    <th class="px-6 py-4 w-[10%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in services?.data ?? []"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        {{ row.service }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ row.unity || '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ row.description || '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                         <p>{{ formatDate(row.createdAt) }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ formatDate(row.updatedAt) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-blue-500 cursor-pointer"
                            title="Visualizar"
                            type="button"
                            @click="openModalShow(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-eye" />
                        </button>
                        <button
                            v-if="canManageServices"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            type="button"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            v-if="canManageServices"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-red-500 cursor-pointer"
                            title="Excluir"
                            type="button"
                            @click="openConfirmDelete(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-trash-can" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div
            v-if="services?.meta && services.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ services.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ services.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in services.meta.links" :key="index">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-4 py-2 text-sm rounded-lg border font-medium transition-all duration-200"
                        :class="[
                            link.active
                                ? 'bg-linear-to-r from-blue-500 to-indigo-600 text-white border-transparent shadow-md hover:shadow-lg'
                                : 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 shadow-sm',
                        ]"
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

    <ViewModal
        v-model="showModal"
        :close-on-esc="true"
        :close-on-back-drop="true"
        :max-width="'2xl'"
        :page-title="pageTitle"
    >
        <ShowContent v-if="context === 'show'" :service="service" />
        <EditContent
            v-else-if="context === 'edit'"
            :service="service"
            :service-unity-select-options="serviceUnitySelectOptions"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :service="service"
            @close="closeModal"
            @delete="deleteService"
        />
    </ViewModal>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import ViewModal from '@/Components/ViewModal.vue';
import FilterBar from '@/Components/FilterBar.vue';
import ShowContent from './ShowContent.vue';
import EditContent from './EditContent.vue';
import ConfirmDelete from './ConfirmDelete.vue';

const page = usePage();

const props = defineProps({
    services: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    canManageServices: {
        type: Boolean,
        default: false,
    },
    serviceUnitySelectOptions: {
        type: Array,
        default: () => [],
    },
});

const serviceFilterFields = [
    { key: 'service', label: 'Serviço' },
    { key: 'created_at', label: 'Data de Cadastro' },
    { key: 'updated_at', label: 'Data de Edição' },
];

const serviceSearchFields = [
    { key: 'service', label: 'Serviço' },
];

const detectFilterField = (f) => {
    if (!f) {
        return 'service';
    }
    if (f.service) {
        return 'service';
    }
    if (f.created_at) {
        return 'created_at';
    }
    if (f.updated_at) {
        return 'updated_at';
    }
    return 'service';
};

const extractFilterValue = (f, field) => {
    if (!f) {
        return '';
    }
    const raw = f[field];
    if (raw === undefined || raw === null) {
        return '';
    }
    return String(raw);
};

const filters = reactive({
    filterField: 'service',
    filterValue: '',
    search: '',
    sortField: 'service',
    sortDirection: 'asc',
});

const syncFiltersFromBackend = (next) => {
    if (!next) {
        return;
    }
    const field = detectFilterField(next);
    filters.filterField = field;
    filters.filterValue = extractFilterValue(next, field);
    filters.search = next.search ? String(next.search) : '';
    filters.sortField = next.sortField ? String(next.sortField) : 'service';
    filters.sortDirection = next.sortDirection ? String(next.sortDirection) : 'asc';
};

watch(
    () => props.services?.filters,
    (next) => {
        syncFiltersFromBackend(next);
    },
    { immediate: true, deep: true },
);

const formatDate = (iso) => {
    if (!iso) {
        return '-';
    }
    try {
        const d = new Date(iso);
        return d.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
    } catch {
        return '-';
    }
};

const applyFilters = (nextFilters) => {
    if (nextFilters && typeof nextFilters === 'object') {
        if (nextFilters.filterField !== undefined) {
            filters.filterField = nextFilters.filterField;
        }
        if (nextFilters.filterValue !== undefined) {
            filters.filterValue = nextFilters.filterValue;
        }
        if (nextFilters.search !== undefined) {
            filters.search = nextFilters.search;
        }
        if (nextFilters.sortField !== undefined) {
            filters.sortField = nextFilters.sortField;
        }
        if (nextFilters.sortDirection !== undefined) {
            filters.sortDirection = nextFilters.sortDirection;
        }
    }

    let rawFilterValue = '';
    if (typeof filters.filterValue === 'string') {
        rawFilterValue = filters.filterValue.trim();
    }

    let rawSearch = '';
    if (typeof filters.search === 'string') {
        rawSearch = filters.search.trim();
    }

    const params = {
        page: 1,
        sortField: filters.sortField,
        sortDirection: filters.sortDirection,
    };

    if (rawSearch !== '') {
        params.search = rawSearch;
    }

    const ff = filters.filterField;
    if (rawFilterValue !== '') {
        params[ff] = rawFilterValue;
    }

    router.get(route('services.index'), params, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'service';
    filters.filterValue = '';
    filters.search = '';
    filters.sortField = 'service';
    filters.sortDirection = 'asc';
    applyFilters();
};

const showModal = ref(false);

const form = useForm({
    confirm: true,
});

const goToCreate = () => {
    router.get(route('services.create'));
};

const goToServiceUnity = () => {
    router.get(route('unity-service.index'));
};

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    loadService(id);
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    loadService(id);
};

const openModalEdit = (id) => {
    context.value = 'edit';
    showModal.value = true;
    loadService(id);
};

const closeModal = () => {
    showModal.value = false;
};

const deleteService = (id) => {
    form.delete(route('services.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

const loadService = (id) => {
    window.axios
        .get(route('services.show', id), {
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            service.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const context = ref('show');

const pageTitle = 'Serviços';

const service = ref({});
</script>
