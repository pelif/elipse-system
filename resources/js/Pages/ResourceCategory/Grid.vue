<template>
    <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-tags" class="mr-2" />
                Categorias
            </h4>

            <button
                v-if="canManageResourceCategories"
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar categoria"
                type="button"
                @click="goToCreate"
            >
                <font-awesome-icon icon="fa-solid fa-square-plus " />
            </button>
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
            :filter-fields="categoryFilterFields"
            :search-fields="categorySearchFields"
            :sort-fields="categorySortFields"
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
        />

        <div v-if="resourceCategories?.data && resourceCategories.data.length === 0" class="p-6 text-center">
            Nenhuma categoria cadastrada.
        </div>

        <table v-else class="w-full text-left border-collapse min-w-[40rem]">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4">Nome</th>
                    <th class="px-6 py-4">Descrição</th>
                    <th class="px-6 py-4 w-[12%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in resourceCategories?.data ?? []"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium wrap-break-word">
                        {{ row.name }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ truncateCell(row.description, 64) }}
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
                            v-if="canManageResourceCategories"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            type="button"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            v-if="canManageResourceCategories"
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
            v-if="resourceCategories?.meta && resourceCategories.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ resourceCategories.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ resourceCategories.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in resourceCategories.meta.links" :key="index">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-4 py-2 text-sm rounded-lg border font-medium transition-all duration-200"
                        :class="paginationLinkClass(link.active)"
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
        max-width="50%"
        :page-title="pageTitle"
    >
        <ShowContent v-if="context === 'show'" :resource-category="resourceCategory" />
        <EditContent
            v-else-if="context === 'edit'"
            :resource-category="resourceCategory"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :resource-category="resourceCategory"
            @close="closeModal"
            @delete="deleteResourceCategory"
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
    resourceCategories: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    canManageResourceCategories: {
        type: Boolean,
        default: false,
    },
});

const categoryFilterFields = [
    { key: 'name', label: 'Nome' },
    { key: 'description', label: 'Descrição' },
];

const categorySearchFields = categoryFilterFields;

const categorySortFields = [
    { key: 'name', label: 'Nome' },
    { key: 'description', label: 'Descrição' },
    { key: 'id', label: 'Identificador' },
];

const detectFilterField = (f) => {
    if (!f) {
        return 'name';
    }
    if (f.name) {
        return 'name';
    }
    if (f.description) {
        return 'description';
    }
    return 'name';
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
    filterField: 'name',
    filterValue: '',
    search: '',
    sortField: 'name',
    sortDirection: 'asc',
});

const syncFiltersFromBackend = (next) => {
    if (!next) {
        return;
    }
    const field = detectFilterField(next);
    filters.filterField = field;
    filters.filterValue = extractFilterValue(next, field);
    let searchVal = '';
    if (next.search !== undefined && next.search !== null) {
        searchVal = String(next.search);
    }
    filters.search = searchVal;
    let sortF = 'name';
    if (next.sortField !== undefined && next.sortField !== null && next.sortField !== '') {
        sortF = String(next.sortField);
    }
    filters.sortField = sortF;
    let sortD = 'asc';
    if (next.sortDirection !== undefined && next.sortDirection !== null && next.sortDirection !== '') {
        sortD = String(next.sortDirection);
    }
    filters.sortDirection = sortD;
};

watch(
    () => props.resourceCategories?.filters,
    (next) => {
        syncFiltersFromBackend(next);
    },
    { immediate: true, deep: true },
);

const truncateCell = (text, maxLen) => {
    if (text === undefined || text === null || String(text).trim() === '') {
        return '-';
    }
    const s = String(text);
    if (s.length <= maxLen) {
        return s;
    }
    return s.slice(0, maxLen) + '…';
};

const paginationLinkClass = (active) => {
    if (active) {
        return 'bg-linear-to-r from-blue-500 to-indigo-600 text-white border-transparent shadow-md hover:shadow-lg';
    }
    return 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 shadow-sm';
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
    } else if (typeof filters.filterValue === 'number') {
        rawFilterValue = String(filters.filterValue);
    } else if (typeof filters.filterValue === 'boolean') {
        if (filters.filterValue) {
            rawFilterValue = '1';
        } else {
            rawFilterValue = '0';
        }
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

    router.get(route('resource-categories.index'), params, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'name';
    filters.filterValue = '';
    filters.search = '';
    filters.sortField = 'name';
    filters.sortDirection = 'asc';
    applyFilters();
};

const showModal = ref(false);

const form = useForm({
    confirm: true,
});

const goToCreate = () => {
    router.get(route('resource-categories.create'));
};

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    loadResourceCategory(id);
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    loadResourceCategory(id);
};

const openModalEdit = (id) => {
    context.value = 'edit';
    showModal.value = true;
    loadResourceCategory(id);
};

const closeModal = () => {
    showModal.value = false;
};

const deleteResourceCategory = (id) => {
    form.delete(route('resource-categories.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

const loadResourceCategory = (id) => {
    window.axios
        .get(route('resource-categories.show', id), {
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            resourceCategory.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const context = ref('show');

const pageTitle = 'Categorias';

const resourceCategory = ref({});
</script>
