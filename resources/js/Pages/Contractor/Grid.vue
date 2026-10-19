<template>
    <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-address-book" class="mr-2" />
                Contratantes
            </h4>

            <button
                v-if="canManageContractors"
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar contratante"
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
            :filter-fields="contractorFilterFields"
            :search-fields="contractorSearchFields"
            :sort-fields="contractorSortFields"
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
        />

        <div v-if="contractors?.data && contractors.data.length === 0" class="p-6 text-center">
            Nenhum contratante cadastrado.
        </div>

        <table v-else class="w-full text-left border-collapse min-w-[56rem]">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4">Nome</th>
                    <th class="px-6 py-4">Telefone</th>
                    <th class="px-6 py-4">E-mail</th>
                    <th class="px-6 py-4">Cliente</th>
                    <th class="px-6 py-4">Cuidados de comunicação</th>
                    <th class="px-6 py-4">Cadastro</th>
                    <th class="px-6 py-4 w-[12%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in contractors?.data ?? []"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium wrap-break-word">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded flex items-center justify-center text-white font-bold text-xs shrink-0"
                                :class="randomBg()"
                            >
                                {{ printInitials(row.name || '') }}
                            </div>
                            <span class="min-w-0 wrap-break-word">{{ row.name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ displayPhone(row.phone) }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ row.email }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ displayCustomerName(row) }}
                    </td>
                    <td class="px-6 py-4 text-sm wrap-break-word">
                        {{ truncateCell(row.communicationCare, 48) }}
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
                            v-if="canManageContractors"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            type="button"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            v-if="canManageContractors"
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
            v-if="contractors?.meta && contractors.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ contractors.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ contractors.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in contractors.meta.links" :key="index">
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
        <ShowContent v-if="context === 'show'" :contractor="contractor" />
        <EditContent
            v-else-if="context === 'edit'"
            :contractor="contractor"
            :customers="customers"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :contractor="contractor"
            @close="closeModal"
            @delete="deleteContractor"
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
import { formatCellphone } from '@/helpers/masks';
import { randomBg, printInitials } from '@/helpers/styles';

const page = usePage();

const props = defineProps({
    contractors: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    customers: {
        type: Array,
        default: () => [],
    },
    canManageContractors: {
        type: Boolean,
        default: false,
    },
});

const contractorFilterFields = [
    { key: 'name', label: 'Nome' },
    { key: 'phone', label: 'Telefone' },
    { key: 'email', label: 'E-mail' },
    { key: 'company_name', label: 'Cliente' },
    { key: 'communication_care', label: 'Cuidados de comunicação' },
];

const contractorSearchFields = contractorFilterFields;

const contractorSortFields = [
    { key: 'name', label: 'Nome' },
    { key: 'phone', label: 'Telefone' },
    { key: 'email', label: 'E-mail' },
    { key: 'customer_name', label: 'Cliente' },
    { key: 'communication_care', label: 'Cuidados de comunicação' },
    { key: 'created_at', label: 'Data de cadastro' },
    { key: 'updated_at', label: 'Última atualização' },
    { key: 'id', label: 'Identificador' },
];

const detectFilterField = (f) => {
    if (!f) {
        return 'name';
    }
    if (f.name) {
        return 'name';
    }
    if (f.phone) {
        return 'phone';
    }
    if (f.email) {
        return 'email';
    }
    if (f.company_name || f.companyName || f.customer || f.customerName) {
        return 'company_name';
    }
    if (f.communication_care || f.communicationCare) {
        return 'communication_care';
    }
    return 'name';
};

const extractFilterValue = (f, field) => {
    if (!f) {
        return '';
    }
    if (field === 'company_name') {
        const keys = ['company_name', 'companyName', 'customer', 'customerName'];
        for (let i = 0; i < keys.length; i++) {
            const k = keys[i];
            const v = f[k];
            if (v !== undefined && v !== null && String(v).trim() !== '') {
                return String(v);
            }
        }
        return '';
    }
    if (field === 'communication_care') {
        const a = f.communication_care;
        const b = f.communicationCare;
        if (a !== undefined && a !== null && String(a) !== '') {
            return String(a);
        }
        if (b !== undefined && b !== null && String(b) !== '') {
            return String(b);
        }
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
    () => props.contractors?.filters,
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

const displayPhone = (raw) => {
    if (!raw) {
        return '-';
    }
    const digits = String(raw).replace(/\D/g, '');
    if (digits === '') {
        return '-';
    }
    return formatCellphone(digits);
};

const displayCustomerName = (row) => {
    if (!row) {
        return '-';
    }
    if (row.customerName != null && String(row.customerName).trim() !== '') {
        return String(row.customerName);
    }
    return '-';
};

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

    router.get(route('contractors.index'), params, {
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
    router.get(route('contractors.create'));
};

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    loadContractor(id);
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    loadContractor(id);
};

const openModalEdit = (id) => {
    context.value = 'edit';
    showModal.value = true;
    loadContractor(id);
};

const closeModal = () => {
    showModal.value = false;
};

const deleteContractor = (id) => {
    form.delete(route('contractors.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

const loadContractor = (id) => {
    window.axios
        .get(route('contractors.show', id), {
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            contractor.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const context = ref('show');

const pageTitle = 'Contratantes';

const contractor = ref({});
</script>
