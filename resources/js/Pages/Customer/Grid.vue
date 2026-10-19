<template>
    <div
        class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
    >
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-user" class="mr-2" />
                Clientes
            </h4>

            <button
                v-if="canManageCustomers"
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar cliente"
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
            :filter-fields="customerFilterFields"
            :search-fields="customerSearchFields"
            :sort-fields="customerSortFields"
            @applyFilters="applyFilters"
            @clearFilters="clearFilters"
        />

        <div v-if="customers?.data && customers.data.length === 0" class="p-6 text-center">
            Nenhum cliente cadastrado.
        </div>

        <table v-else class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4 w-[25%]">CLIENTE</th>
                    <th class="px-6 py-4 w-[15%]">REFERÊNCIAS</th>
                    <th class="px-6 py-4 w-[18%]">RELACIONAMENTOS</th>
                    <th class="px-6 py-4 w-[8%]">STATUS</th>
                    <th class="px-6 py-4">Atualização</th>
                    <th class="px-6 py-4 w-[1%] whitespace-nowrap">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in customers?.data ?? []"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        <div class="flex items-start gap-2">
                            <div
                                class="w-8 h-8 rounded flex items-center justify-center text-white font-bold text-xs shrink-0"
                                :class="randomBg()"
                            >
                                {{ printInitials(row.companyName || '') }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-lg font-bold">{{ row.companyName }}</p>
                                <p class="text-sx text-gray-400">{{ row.legalName }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <p class="text-lg font-bold">{{ row.codeCustomer }}</p>
                        <p class="text-sx text-gray-400">{{ formatCnpj(row.document) }}</p>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ row.jobsCount }} Jobs -
                        {{ row.contractorsCount }} Contratantes -
                        {{ row.organizersCount }} Organizadores
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <p
                            class="px-6 py-1.5  rounded-full"
                            :class="row.active ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'">
                        {{ row.active ? 'Ativo' : 'Inativo' }}
                        </p>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ formatDate(row.updatedAt) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-blue-500 cursor-pointer"
                            title="Visualizar"
                            type="button"
                            @click="openModalShow(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-eye" />
                        </button>
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-teal-600 cursor-pointer"
                            title="Informações financeiras"
                            type="button"
                            @click="openFinancialModal(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-circle-info" />
                        </button>
                        <button
                            v-if="canManageCustomers"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            type="button"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            v-if="canManageCustomers"
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
            v-if="customers?.meta && customers.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ customers.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ customers.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in customers.meta.links" :key="index">
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
        :max-width="'65%'"
        :page-title="pageTitleModal"
    >
        <ShowContent v-if="context === 'show'" :customer="customer" :in-modal="true" />
        <EditContent
            v-else-if="context === 'edit'"
            :customer="customer"
            :comissioned-agent-options="comissionedAgentOptions"
            @close="closeModal"
        />
        <ConfirmDelete v-else :customer="customer" @close="closeModal" @delete="deleteCustomer" />
    </ViewModal>

    <ViewModal
        v-model="showFinancialModal"
        :close-on-esc="true"
        :close-on-back-drop="true"
        :max-width="'45%'"
        page-title="Informações financeiras"
    >
        <FinancialInfoContent
            :customer="financialCustomer"
            :can-manage="canManageCustomers"
            @close="closeFinancialModal"
        />
    </ViewModal>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import ViewModal from '@/Components/ViewModal.vue';
import FilterBar from '@/Components/FilterBar.vue';
import ShowContent from './ShowContent.vue';
import EditContent from './EditContent.vue';
import ConfirmDelete from './ConfirmDelete.vue';
import FinancialInfoContent from './FinancialInfoContent.vue';
import { randomBg, printInitials } from '@/helpers/styles';

const FILTER_FIELD_PAIRS = [
    ['company_name', 'companyName'],
    ['legal_name', 'legalName'],
    ['document', 'document'],
    ['code_customer', 'codeCustomer'],
    ['active', 'active'],
    ['updated_at', 'updatedAt'],
];

const customerFilterFields = [
    { key: 'company_name', label: 'Nome fantasia' },
    { key: 'legal_name', label: 'Razão social' },
    { key: 'document', label: 'CNPJ' },
    { key: 'code_customer', label: 'Código' },
    { key: 'active', label: 'Ativo' },
    { key: 'updated_at', label: 'Atualização (data)' },
];

const customerSearchFields = customerFilterFields;

const customerSortFields = [
    { key: 'id', label: 'ID' },
    { key: 'company_name', label: 'Nome fantasia' },
    { key: 'legal_name', label: 'Razão social' },
    { key: 'document', label: 'CNPJ' },
    { key: 'code_customer', label: 'Código' },
    { key: 'active', label: 'Ativo' },
    { key: 'created_at', label: 'Data de Cadastro' },
    { key: 'updated_at', label: 'Data de Atualização' },
];

const page = usePage();

const props = defineProps({
    customers: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    canManageCustomers: {
        type: Boolean,
        default: false,
    },
});

console.log(props.customers);

const initialFilters = computed(() => props.customers?.filters ?? {});

function inferFilterPair(filters) {
    if (!filters || typeof filters !== 'object') {
        return { filterField: 'company_name', filterValue: '' };
    }
    for (const [snake, camel] of FILTER_FIELD_PAIRS) {
        const v = filters[camel];
        if (v !== undefined && v !== null && String(v).trim() !== '') {
            const fv = typeof v === 'boolean' ? (v ? '1' : '0') : String(v);
            return { filterField: snake, filterValue: fv };
        }
    }
    return { filterField: 'company_name', filterValue: '' };
}

const filters = reactive({
    filterField: 'company_name',
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
        filters.search = next.search ?? '';
        filters.sortField = next.sortField ?? 'updated_at';
        filters.sortDirection = next.sortDirection ?? 'desc';
    },
    { immediate: true },
);

const formatDate = (iso) => {
    if (!iso) return '-';
    try {
        const d = new Date(iso);
        return d.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' });
    } catch {
        return '-';
    }
};

const formatCnpj = (raw) => {
    if (!raw) return '-';
    const d = String(raw).replace(/\D/g, '').slice(0, 14);
    if (d.length !== 14) return String(raw);
    return d.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
};

const applyFilters = (nextFilters) => {
    if (nextFilters && typeof nextFilters === 'object') {
        filters.filterField = nextFilters.filterField ?? filters.filterField;
        filters.filterValue = nextFilters.filterValue ?? filters.filterValue;
        filters.search = nextFilters.search ?? filters.search;
        filters.sortField = nextFilters.sortField ?? filters.sortField;
        filters.sortDirection = nextFilters.sortDirection ?? filters.sortDirection;
    }

    let rawFilterValue = '';
    if (typeof filters.filterValue === 'string') rawFilterValue = filters.filterValue.trim();
    else if (typeof filters.filterValue === 'number') rawFilterValue = String(filters.filterValue);
    else if (typeof filters.filterValue === 'boolean') rawFilterValue = filters.filterValue ? '1' : '0';

    let rawSearch = '';
    if (typeof filters.search === 'string') {
        rawSearch = filters.search.trim();
    }

    const params = {
        page: 1,
        search: rawSearch !== '' ? rawSearch : undefined,
        sortField: filters.sortField,
        sortDirection: filters.sortDirection,
        company_name: filters.filterField === 'company_name' && rawFilterValue !== '' ? rawFilterValue : undefined,
        legal_name: filters.filterField === 'legal_name' && rawFilterValue !== '' ? rawFilterValue : undefined,
        document: filters.filterField === 'document' && rawFilterValue !== '' ? rawFilterValue : undefined,
        code_customer: filters.filterField === 'code_customer' && rawFilterValue !== '' ? rawFilterValue : undefined,
        active: filters.filterField === 'active' && rawFilterValue !== '' ? rawFilterValue : undefined,
        updated_at: filters.filterField === 'updated_at' && rawFilterValue !== '' ? rawFilterValue : undefined,
    };

    router.get(route('customers.index'), params, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'company_name';
    filters.filterValue = '';
    filters.search = '';
    filters.sortField = 'updated_at';
    filters.sortDirection = 'desc';
    applyFilters();
};

const showModal = ref(false);
const showFinancialModal = ref(false);

const form = useForm({
    confirm: true,
});

const goToCreate = () => {
    router.get(route('customers.create'));
};

const customer = ref({});
const financialCustomer = ref({});
const comissionedAgentOptions = ref([]);

const loadCustomerJson = (id) =>
    window.axios.get(route('customers.show', id), { headers: { Accept: 'application/json' } }).then((response) => {
        customer.value = response.data;
    });

const loadFinancialCustomerJson = (id) =>
    window.axios.get(route('customers.show', id), { headers: { Accept: 'application/json' } }).then((response) => {
        financialCustomer.value = response.data;
    });

const context = ref('show');

const pageTitleModal = computed(() => {
    if (context.value === 'edit') return 'Editar cliente';
    if (context.value === 'delete') return 'Excluir cliente';
    return 'Cliente';
});

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    loadCustomerJson(id).catch(() => {});
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    loadCustomerJson(id).catch(() => {});
};

const openModalEdit = (id) => {
    context.value = 'edit';
    showModal.value = true;
    loadCustomerJson(id).catch(() => {});
};

const closeModal = () => {
    showModal.value = false;
};

const openFinancialModal = (id) => {
    financialCustomer.value = {};
    showFinancialModal.value = true;
    loadFinancialCustomerJson(id).catch(() => {});
};

const closeFinancialModal = () => {
    showFinancialModal.value = false;
};

const deleteCustomer = (id) => {
    form.delete(route('customers.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

onMounted(() => {
    if (!props.canManageCustomers) {
        return;
    }
    window.axios
        .get(route('comissioned-agents.for-select'), { headers: { Accept: 'application/json' } })
        .then((response) => {
            comissionedAgentOptions.value = Array.isArray(response.data) ? response.data : [];
        })
        .catch(() => {
            comissionedAgentOptions.value = [];
        });
});
</script>
