<template>
    <div
        class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
    >
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-address-book" class="mr-2" />
                Terceiros
            </h4>

            <div class="p-6 flex justify-items-end items-end">
                <button
                    class="self-start mt-1.5 text-2xl bg-gray-200 text-sm px-4 py-1 text-gray-500 border border-gray-500 rounded-sm cursor-pointer text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out"
                    title="Serviços"
                    type="button"
                    @click="goToServiceUnity">
                    <font-awesome-icon icon="fa-solid fa-gear"/>
                    Serviços
                </button>

                <button
                    v-if="canManageThirdParties"
                    class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                    title="Adicionar terceiro"
                    type="button"
                    @click="goToCreate">
                    <font-awesome-icon icon="fa-solid fa-square-plus " />
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
            :filter-fields="thirdPartyFilterFields"
            :search-fields="thirdPartySearchFields"
            :sort-fields="thirdPartySortFields"
            @apply-filters="applyFilters"
            @clear-filters="clearFilters"
        />

        <div v-if="thirdParties?.data && thirdParties.data.length === 0" class="p-6 text-center">
            Nenhum terceiro cadastrado.
        </div>

        <table v-else class="w-full text-left border-collapse min-w-[56rem]">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4 w-[32%]">Profissional</th>
                    <th class="px-6 py-4 w-[22%]">Especialidade</th>
                    <th class="px-6 py-4 w-[10%]">Histórico</th>
                    <th class="px-6 py-4 w-[10%]">Ativo</th>
                    <th class="px-6 py-4 w-[14%]">Cadastro</th>
                    <th class="px-6 py-4 w-[12%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in thirdParties?.data ?? []"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium wrap-break-word">
                        <div class="flex items-start gap-2">
                            <div
                                class="w-8 h-8 rounded flex items-center justify-center text-white font-bold text-xs shrink-0"
                                :class="randomBg()"
                            >
                                {{ printInitials(row.name || '') }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-lg font-bold">{{ row.name }}</p>
                                <p class="text-sm text-gray-400">{{ row.alias || '-' }}</p>
                                <p class="text-sm text-gray-400">{{ displayPhone(row.phone) }}</p>
                                <p class="text-sm text-gray-400">{{ row.email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            <span
                                v-if="!rowServices(row).length"
                                class="text-sm text-gray-400"
                            >
                                -
                            </span>
                            <span
                                v-for="service in rowServices(row)"
                                :key="service.id"
                                class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-medium"
                            >
                                {{ service.service }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <p class="font-medium text-gray-800">{{ row.jobsCount || 0 }} jobs</p>
                        <p class="text-sm text-gray-500">Último: {{ row.lastJobCode || '-' }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <p
                            class="px-6 py-1.5 rounded-full inline-block"
                            :class="activeBadgeClass(row.active)"
                        >
                            {{ activeLabel(row.active) }}
                        </p>
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
                            v-if="canManageThirdParties"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            type="button"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            v-if="canManageThirdParties"
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
            v-if="thirdParties?.meta && thirdParties.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ thirdParties.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ thirdParties.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in thirdParties.meta.links" :key="index">
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
        <ShowContent v-if="context === 'show'" :third-party="thirdParty" />
        <EditContent
            v-else-if="context === 'edit'"
            :third-party="thirdParty"
            :service-select-options="serviceSelectOptions"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :third-party="thirdParty"
            @close="closeModal"
            @delete="deleteThirdParty"
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
    thirdParties: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    canManageThirdParties: {
        type: Boolean,
        default: false,
    },
    serviceSelectOptions: {
        type: Array,
        default: () => [],
    },
});

const thirdPartyFilterFields = [
    { key: 'name', label: 'Nome' },
    { key: 'alias', label: 'Apelido' },
    { key: 'email', label: 'E-mail' },
    { key: 'email_alternative', label: 'E-mail alternativo' },
    { key: 'indication', label: 'Indicação' },
    { key: 'observation', label: 'Observações' },
    { key: 'company_name', label: 'Razão social' },
    { key: 'document', label: 'CNPJ' },
    { key: 'active', label: 'Ativo' },
];

const thirdPartySearchFields = thirdPartyFilterFields;

const thirdPartySortFields = [
    { key: 'name', label: 'Nome' },
    { key: 'alias', label: 'Apelido' },
    { key: 'email', label: 'E-mail' },
    { key: 'email_alternative', label: 'E-mail alternativo' },
    { key: 'company_name', label: 'Razão social' },
    { key: 'document', label: 'CNPJ' },
    { key: 'indication', label: 'Indicação' },
    { key: 'observation', label: 'Observações' },
    { key: 'active', label: 'Ativo' },
    { key: 'created_at', label: 'Data de cadastro' },
    { key: 'updated_at', label: 'Última atualização' },
];

const detectFilterField = (f) => {
    if (!f) {
        return 'name';
    }
    if (f.name) {
        return 'name';
    }
    if (f.alias) {
        return 'alias';
    }
    if (f.email) {
        return 'email';
    }
    if (f.email_alternative || f.emailAlternative) {
        return 'email_alternative';
    }
    if (f.indication) {
        return 'indication';
    }
    if (f.observation) {
        return 'observation';
    }
    if (f.company_name || f.companyName) {
        return 'company_name';
    }
    if (f.document) {
        return 'document';
    }
    if (f.active !== undefined && f.active !== null && f.active !== '') {
        return 'active';
    }
    return 'name';
};

const extractFilterValue = (f, field) => {
    if (!f) {
        return '';
    }
    if (field === 'email_alternative') {
        let v = f.email_alternative;
        if (v === undefined || v === null || v === '') {
            v = f.emailAlternative;
        }
        if (v === undefined || v === null) {
            return '';
        }
        return String(v);
    }
    if (field === 'company_name') {
        let v = f.company_name;
        if (v === undefined || v === null || v === '') {
            v = f.companyName;
        }
        if (v === undefined || v === null) {
            return '';
        }
        return String(v);
    }
    if (field === 'active') {
        if (f.active === true) {
            return '1';
        }
        if (f.active === false) {
            return '0';
        }
        if (f.active === undefined || f.active === null) {
            return '';
        }
        return String(f.active);
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
    () => props.thirdParties?.filters,
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

const activeLabel = (active) => {
    if (active === true || active === 1 || active === '1') {
        return 'Sim';
    }
    return 'Não';
};

const activeBadgeClass = (active) => {
    if (active === true || active === 1 || active === '1') {
        return 'bg-green-100 text-green-800';
    }
    return 'bg-orange-100 text-orange-800';
};

const rowServices = (row) => {
    if (!row || !Array.isArray(row.services)) {
        return [];
    }
    return row.services;
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

    router.get(route('third-parties.index'), params, {
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
    router.get(route('third-parties.create'));
};

const goToServiceUnity = () => {
    router.get(route('services.index'));
}

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    loadThirdParty(id);
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    loadThirdParty(id);
};

const openModalEdit = (id) => {
    context.value = 'edit';
    showModal.value = true;
    loadThirdParty(id);
};

const closeModal = () => {
    showModal.value = false;
};

const deleteThirdParty = (id) => {
    form.delete(route('third-parties.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

const loadThirdParty = (id) => {
    window.axios
        .get(route('third-parties.show', id), {
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            thirdParty.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const context = ref('show');

const pageTitle = 'Terceiros';

const thirdParty = ref({});
</script>
