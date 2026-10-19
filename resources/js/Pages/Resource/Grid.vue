<template>
    <div
        class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
    >
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-list" class="mr-2" />
                Recursos
            </h4>

            <button
                v-if="canManageResources"
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar recurso"
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
            :filter-fields="resourceFilterFields"
            :search-fields="resourceSearchFields"
            :sort-fields="resourceSortFields"
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
        />

        <div v-if="resources?.data && resources.data.length === 0" class="p-6 text-center">
            Nenhum recurso cadastrado.
        </div>

        <table v-else class="w-full text-left border-collapse min-w-[64rem]">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4">Nome</th>
                    <th class="px-6 py-4">Código patrimonial</th>
                    <th class="px-6 py-4">Categoria</th>
                    <th class="px-6 py-4">Departamento</th>
                    <th class="px-6 py-4">Fabricante</th>
                    <th class="px-6 py-4">Modelo</th>
                    <th class="px-6 py-4">Nº série</th>
                    <th class="px-6 py-4">Responsável</th>
                    <th class="px-6 py-4">Documentação</th>
                    <th class="px-6 py-4">Cadastro</th>
                    <th class="px-6 py-4 w-[12%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in resources?.data ?? []"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium wrap-break-word">
                        {{ row.resourceName || '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ row.patrimonialCode || '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ resolveOptionLabel(categorySelectOptions, row.categoryId) }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ resolveOptionLabel(departmentSelectOptions, row.departmentId) }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ row.manufacturer || '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ row.model || '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ row.serialNumber || '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ responsibleLabel(row.responsibleId) }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <template v-if="documentationHrefFor(row.documentationLink) !== ''">
                            <a
                                class="text-blue-600 hover:underline"
                                :href="documentationHrefFor(row.documentationLink)"
                                target="_blank"
                                rel="noopener noreferrer"
                            >
                                Abrir
                            </a>
                        </template>
                        <span v-else>-</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ formatDate(row.createdAt) }}
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
                            v-if="canManageResources"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            type="button"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            v-if="canManageResources"
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
            v-if="resources?.meta && resources.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ resources.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ resources.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in resources.meta.links" :key="index">
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
        max-width="58%"
        :page-title="modalTitle"
    >
        <ShowContent v-if="context === 'show'" :resource="resourceDetail" :category-select-options="categorySelectOptions" :department-select-options="departmentSelectOptions" />
        <EditContent
            v-else-if="context === 'edit'"
            :resource="resourceDetail"
            :category-select-options="categorySelectOptions"
            :department-select-options="departmentSelectOptions"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :resource="resourceDetail"
            @close="closeModal"
            @delete="deleteResource"
        />
    </ViewModal>
</template>

<script setup>
import { reactive, ref, watch } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import ViewModal from '@/Components/ViewModal.vue';
import FilterBar from '@/Components/FilterBar.vue';
import { formatDocumentationUrl } from '@/helpers/masks';
import ShowContent from './ShowContent.vue';
import EditContent from './EditContent.vue';
import ConfirmDelete from './ConfirmDelete.vue';

const page = usePage();

const props = defineProps({
    resources: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    canManageResources: {
        type: Boolean,
        default: false,
    },
    categorySelectOptions: {
        type: Array,
        default: () => [],
    },
    departmentSelectOptions: {
        type: Array,
        default: () => [],
    },
});

const resourceFilterFields = [
    { key: 'resource_name', label: 'Nome do recurso' },
    { key: 'category', label: 'Categoria' },
    { key: 'department', label: 'Departamento' },
    { key: 'patrimonial_code', label: 'Código patrimonial' },
    { key: 'manufacturer', label: 'Fabricante' },
    { key: 'model', label: 'Modelo' },
    { key: 'serial_number', label: 'Número de série' },
    { key: 'responsible', label: 'Responsável' },
    { key: 'notes', label: 'Observações' },
    { key: 'documentation_link', label: 'Link de documentação' },
];

const resourceSearchFields = resourceFilterFields;

const resourceSortFields = [
    { key: 'resource_name', label: 'Nome do recurso' },
    { key: 'patrimonial_code', label: 'Código patrimonial' },
    { key: 'category_id', label: 'Categoria (ID)' },
    { key: 'department_id', label: 'Departamento (ID)' },
    { key: 'manufacturer', label: 'Fabricante' },
    { key: 'model', label: 'Modelo' },
    { key: 'serial_number', label: 'Número de série' },
    { key: 'responsible_id', label: 'Responsável (ID)' },
    { key: 'notes', label: 'Observações' },
    { key: 'documentation_link', label: 'Link de documentação' },
    { key: 'created_at', label: 'Data de cadastro' },
    { key: 'updated_at', label: 'Última atualização' },
];

const hasNonEmptyString = (v) => {
    if (v === undefined || v === null) {
        return false;
    }
    return String(v).trim() !== '';
};

const detectFilterField = (f) => {
    if (!f) {
        return 'resource_name';
    }
    if (hasNonEmptyString(f.resource_name)) {
        return 'resource_name';
    }
    if (hasNonEmptyString(f.name)) {
        return 'resource_name';
    }
    if (hasNonEmptyString(f.category_id)) {
        return 'category_id';
    }
    if (hasNonEmptyString(f.category)) {
        return 'category';
    }
    if (hasNonEmptyString(f.department_id)) {
        return 'department_id';
    }
    if (hasNonEmptyString(f.department)) {
        return 'department';
    }
    if (hasNonEmptyString(f.patrimonial_code)) {
        return 'patrimonial_code';
    }
    if (hasNonEmptyString(f.manufacturer)) {
        return 'manufacturer';
    }
    if (hasNonEmptyString(f.model)) {
        return 'model';
    }
    if (hasNonEmptyString(f.serial_number)) {
        return 'serial_number';
    }
    if (hasNonEmptyString(f.responsible)) {
        return 'responsible';
    }
    if (hasNonEmptyString(f.notes)) {
        return 'notes';
    }
    if (hasNonEmptyString(f.documentation_link)) {
        return 'documentation_link';
    }
    return 'resource_name';
};

const extractFilterValue = (f, field) => {
    if (!f) {
        return '';
    }
    if (field === 'resource_name') {
        let v = f.resource_name;
        if (!hasNonEmptyString(v)) {
            v = f.name;
        }
        if (v === undefined || v === null) {
            return '';
        }
        return String(v);
    }
    const raw = f[field];
    if (raw === undefined || raw === null) {
        return '';
    }
    return String(raw);
};

const filters = reactive({
    filterField: 'resource_name',
    filterValue: '',
    search: '',
    sortField: 'resource_name',
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
    let sortF = 'resource_name';
    if (hasNonEmptyString(next.sortField)) {
        sortF = String(next.sortField);
    }
    filters.sortField = sortF;
    let sortD = 'asc';
    if (hasNonEmptyString(next.sortDirection)) {
        sortD = String(next.sortDirection);
    }
    filters.sortDirection = sortD;
};

watch(
    () => props.resources?.filters,
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

const resolveOptionLabel = (options, id) => {
    if (!id || !options || options.length === 0) {
        return '-';
    }
    const sid = String(id);
    for (let i = 0; i < options.length; i++) {
        if (String(options[i].id) === sid) {
            return options[i].label;
        }
    }
    return '-';
};

const responsibleLabel = (rid) => {
    if (rid === undefined || rid === null) {
        return '-';
    }
    return String(rid);
};

const documentationHrefFor = (raw) => {
    if (!raw) {
        return '';
    }
    return formatDocumentationUrl(String(raw));
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

    router.get(route('resources.index'), params, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'resource_name';
    filters.filterValue = '';
    filters.search = '';
    filters.sortField = 'resource_name';
    filters.sortDirection = 'asc';
    applyFilters();
};

const showModal = ref(false);
const context = ref('show');
const modalTitle = ref('Recursos');
const resourceDetail = ref({});

const form = useForm({
    confirm: true,
});

const goToCreate = () => {
    router.get(route('resources.create'));
};

const openModalShow = (id) => {
    context.value = 'show';
    modalTitle.value = 'Detalhes do recurso';
    showModal.value = true;
    loadResource(id);
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    modalTitle.value = 'Confirmação de exclusão';
    showModal.value = true;
    loadResource(id);
};

const openModalEdit = (id) => {
    context.value = 'edit';
    modalTitle.value = 'Editar recurso';
    showModal.value = true;
    loadResource(id);
};

const closeModal = () => {
    showModal.value = false;
};

const deleteResource = (id) => {
    form.delete(route('resources.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

const loadResource = (id) => {
    window.axios
        .get(route('resources.show', id), {
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            resourceDetail.value = response.data;
        })
        .catch(() => {
            resourceDetail.value = {};
        });
};
</script>
