<template>
    <div
        class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
    >
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-award" class="mr-2" />
                Comissionados
            </h4>

            <button
                v-if="canManageComissionedAgents"
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar comissionado"
                type="button"
                @click="goToCreate"
            >
                <font-awesome-icon icon="fa-solid fa-square-plus" />
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
            :filter-fields="[
                { key: 'name', label: 'Nome' },
                { key: 'defaultComission', label: 'Comissão Padrão (%)' },
                { key: 'paymentDays', label: 'Dias de Pagamento' },
            ]"
            :sort-fields="[
                { key: 'name', label: 'Nome' },
                { key: 'defaultComission', label: 'Comissão Padrão (%)' },
                { key: 'paymentDays', label: 'Dias de Pagamento' },
                { key: 'createdAt', label: 'Data de cadastro' },
            ]"
            :search-fields="[]"
            :show-search="false"
            @applyFilters="applyFilters"
            @clearFilters="clearFilters"
        />

        <div v-if="comissionedAgents?.data && comissionedAgents.data.length === 0" class="p-6 text-center">
            Nenhum comissionado cadastrado.
        </div>

        <table v-else class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4 w-[30%]">Nome</th>
                    <th class="px-6 py-4 w-[10%]">Comissão (%)</th>
                    <th class="px-6 py-4 w-[10%]">Dias Pgto.</th>
                    <th class="px-6 py-4 w-[35%]">Cadastro</th>
                    <th class="px-6 py-4 w-[15%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in comissionedAgents?.data ?? []"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
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
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ row.defaultComission }}%
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ row.paymentDays }}
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
                            v-if="canManageComissionedAgents"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            type="button"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            v-if="canManageComissionedAgents"
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
            v-if="comissionedAgents?.meta && comissionedAgents.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ comissionedAgents.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ comissionedAgents.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in comissionedAgents.meta.links" :key="index">
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
        :max-width="'50%'"
        :page-title="pageTitle"
    >
        <ShowContent v-if="context === 'show'" :comissioned-agent="comissionedAgent" />
        <EditContent
            v-else-if="context === 'edit'"
            :comissioned-agent="comissionedAgent"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :comissioned-agent="comissionedAgent"
            @close="closeModal"
            @delete="deleteComissionedAgent"
        />
    </ViewModal>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Link, useForm, usePage, router } from '@inertiajs/vue3';
import ViewModal from '@/Components/ViewModal.vue';
import FilterBar from '@/Components/FilterBar.vue';
import ShowContent from './ShowContent.vue';
import EditContent from './EditContent.vue';
import ConfirmDelete from './ConfirmDelete.vue';
import { randomBg, printInitials } from '@/helpers/styles';

const page = usePage();

const props = defineProps({
    comissionedAgents: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    canManageComissionedAgents: {
        type: Boolean,
        default: false,
    },
});

const initialFilters = computed(() => props.comissionedAgents?.filters ?? {});

const resolveInitialFilterField = (next) => {
    const order = ['name', 'defaultComission', 'paymentDays'];
    for (const key of order) {
        const val = next?.[key];
        if (val !== null && val !== undefined && String(val).trim() !== '') {
            return key;
        }
    }
    return 'name';
};

const resolveInitialFilterValue = (next) => {
    const field = resolveInitialFilterField(next);
    const val = next?.[field];
    if (val !== null && val !== undefined) {
        return String(val);
    }
    return '';
};

const filters = reactive({
    filterField: resolveInitialFilterField(initialFilters.value),
    filterValue: resolveInitialFilterValue(initialFilters.value),
    search: '',
    sortField: initialFilters.value.sortField ?? 'createdAt',
    sortDirection: initialFilters.value.sortDirection ?? 'desc',
});

watch(
    () => props.comissionedAgents?.filters,
    (next) => {
        if (!next) return;
        filters.filterField = resolveInitialFilterField(next);
        filters.filterValue = resolveInitialFilterValue(next);
        filters.sortField = next.sortField ?? 'createdAt';
        filters.sortDirection = next.sortDirection ?? 'desc';
    },
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
    } else if (typeof filters.filterValue === 'number') {
        rawFilterValue = String(filters.filterValue);
    }

    const query = {
        page: 1,
        name: undefined,
        defaultComission: undefined,
        paymentDays: undefined,
        sortField: filters.sortField,
        sortDirection: filters.sortDirection,
    };

    if (rawFilterValue !== '') {
        query[filters.filterField] = rawFilterValue;
    }

    router.get(route('comissioned-agents.index'), query, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'name';
    filters.filterValue = '';
    filters.sortField = 'createdAt';
    filters.sortDirection = 'desc';
    applyFilters();
};

const showModal = ref(false);
const context = ref('show');
const pageTitle = 'Comissionados';
const comissionedAgent = ref({});

const form = useForm({ confirm: true });

const goToCreate = () => {
    router.get(route('comissioned-agents.create'));
};

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    loadComissionedAgent(id);
};

const openModalEdit = (id) => {
    context.value = 'edit';
    showModal.value = true;
    loadComissionedAgent(id);
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    loadComissionedAgent(id);
};

const closeModal = () => {
    showModal.value = false;
};

const deleteComissionedAgent = (id) => {
    form.delete(route('comissioned-agents.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

const loadComissionedAgent = (id) => {
    window.axios
        .get(route('comissioned-agents.show', id), {
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            comissionedAgent.value = response.data;
        })
        .catch((error) => {
            console.error(error);
        });
};
</script>
