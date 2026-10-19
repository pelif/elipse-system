<template>
    <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-envelope" class="mr-2" />
                Configuração de E-mail
            </h4>

            <Link
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar Configuração de E-mail"
                :href="route('email-config.create')"
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
            :filterFields="[
                { key: 'address', label: 'E-mail' },
                { key: 'departmentId', label: 'Departamento' },
            ]"
            :searchFields="[
                { key: 'address', label: 'E-mail' },
                { key: 'department', label: 'Departamento' },
            ]"
            @applyFilters="applyFilters"
            @clearFilters="clearFilters"
        />

        <div v-if="emailConfigs?.data && emailConfigs.data.length === 0" class="p-6 text-center">
            Nenhuma configuração de e-mail cadastrada.
        </div>

        <table v-else class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4 w-[25%]">E-mail</th>
                    <th class="px-6 py-4 w-[60%]">Departamento</th>
                    <th class="px-6 py-4 w-[15%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in emailConfigs.data"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        {{ row.address }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        {{ departmentName(row.departmentId) ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-blue-500 cursor-pointer"
                            title="Visualizar"
                            @click="openModalShow(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-eye" />
                        </button>
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-red-800 cursor-pointer"
                            title="Excluir"
                            @click="openConfirmDelete(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-trash-can" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div
            v-if="emailConfigs?.meta && emailConfigs.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ emailConfigs.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ emailConfigs.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in emailConfigs.meta.links" :key="index">
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
        :closeOnEsc="true"
        :closeOnBackDrop="true"
        :maxWidth="'50%'"
        :pageTitle="viewPageTitle"
    >
        <ShowContent v-if="context === 'show'" :emailConfig="current" :departments="departments" />
        <EditContent
            v-else-if="context === 'edit'"
            :emailConfig="current"
            :departments="departments"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :emailConfig="current"
            @close="closeModal"
            @delete="deleteEmailConfig"
        />
    </ViewModal>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import ViewModal from '@/Components/ViewModal.vue';
import FilterBar from '@/Components/FilterBar.vue';
import ShowContent from './Partials/ShowContent.vue';
import EditContent from './Partials/EditContent.vue';
import ConfirmDelete from './Partials/ConfirmDelete.vue';

const page = usePage();

const props = defineProps({
    emailConfigs: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    departments: {
        type: Array,
        default: () => [],
    },
});

const initialFilters = computed(() => props.emailConfigs?.filters ?? {});

const filters = reactive({
    filterField: initialFilters.value.address ? 'address' : initialFilters.value.departmentId ? 'departmentId' : 'address',
    filterValue: initialFilters.value.address ?? initialFilters.value.departmentId ?? '',
    search: initialFilters.value.search ?? '',
    sortField: initialFilters.value.sortField ?? 'address',
    sortDirection: initialFilters.value.sortDirection ?? 'asc',
});

watch(
    () => props.emailConfigs?.filters,
    (next) => {
        if (!next) return;
        filters.filterField = next.address ? 'address' : next.departmentId ? 'departmentId' : 'address';
        filters.filterValue = next.address ?? next.departmentId ?? '';
        filters.search = next.search ?? '';
        filters.sortField = next.sortField ?? 'address';
        filters.sortDirection = next.sortDirection ?? 'asc';
    }
);

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

    let rawSearch = '';
    if (typeof filters.search === 'string') rawSearch = filters.search.trim();

    const query = {
        page: 1,
        address: undefined,
        departmentId: undefined,
        search: rawSearch !== '' ? rawSearch : undefined,
        sortField: filters.sortField,
        sortDirection: filters.sortDirection,
    };

    if (rawFilterValue !== '') {
        query[filters.filterField] = rawFilterValue;
    }

    router.get(route('email-config.index'), query, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'address';
    filters.filterValue = '';
    filters.search = '';
    filters.sortField = 'address';
    filters.sortDirection = 'asc';
    applyFilters();
};

const departmentName = (id) => {
    if (!id) return null;
    const d = props.departments.find((x) => x.id === id);
    return d ? d.department : null;
};

const showModal = ref(false);
const context = ref('show');
const viewPageTitle = ref('Config. de E-mail');
const current = ref({});

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    fetchItem(id);
};

const openModalEdit = (id) => {
    context.value = 'edit';
    showModal.value = true;
    fetchItem(id);
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    fetchItem(id);
};

const closeModal = () => {
    showModal.value = false;
};

const fetchItem = (id) => {
    window.axios
        .get(route('email-config.show', id))
        .then((response) => {
            current.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const form = useForm({});

const deleteEmailConfig = (id) => {
    form.delete(route('email-config.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};
</script>

