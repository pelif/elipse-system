<template>
    <div
        class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
    >
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-box" class="mr-2" />
                Produtos
            </h4>

            <button
                v-if="canManageProducts"
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar produto"
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
            :filter-fields="[
                { key: 'name', label: 'Produto' },
                { key: 'created_at', label: 'Data de Cadastro' },
            ]"
            :search-fields="[
                { key: 'name', label: 'Produto' },
                { key: 'created_at', label: 'Data de Cadastro' },
            ]"
            @applyFilters="applyFilters"
            @clearFilters="clearFilters"
        />

        <div
            v-if="products?.data && products.data.length === 0"
            class="p-6 text-center">
            Nenhum produto cadastrado.
        </div>

        <table v-else class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4 w-[60%]">Produto</th>
                    <th class="px-6 py-4 w-[25%]">Cadastro</th>
                    <th class="px-6 py-4 w-[15%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in products?.data ?? []"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                        {{ row.name }}
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
                            v-if="canManageProducts"
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            type="button"
                            @click="openModalEdit(row.id)"
                        >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            v-if="canManageProducts"
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
            v-if="products?.meta && products.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{ products.meta.current_page }}</span>
                de
                <span class="font-medium text-gray-900">{{ products.meta.last_page }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in products.meta.links" :key="index">
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
        <ShowContent v-if="context === 'show'" :product="product" />
        <EditContent
            v-else-if="context === 'edit'"
            :product="product"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :product="product"
            @close="closeModal"
            @delete="deleteProduct"
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

const page = usePage();
const props = defineProps({
    products: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
    canManageProducts: {
        type: Boolean,
        default: false,
    },
});

const initialFilters = computed(() => props.products?.filters ?? {});

const filters = reactive({
    filterField: initialFilters.value.name ? 'name' : 'name',
    filterValue: initialFilters.value.name ?? '',
    search: initialFilters.value.search ?? '',
    sortField: initialFilters.value.sortField ?? 'name',
    sortDirection: initialFilters.value.sortDirection ?? 'asc',
});

watch(
    () => props.products?.filters,
    (next) => {
        if (!next) return;

        filters.filterField = 'name';
        filters.filterValue = next.name ?? '';
        filters.search = next.search ?? '';
        filters.sortField = next.sortField ?? 'name';
        filters.sortDirection = next.sortDirection ?? 'asc';
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
        filters.search = nextFilters.search ?? filters.search;
        filters.sortField = nextFilters.sortField ?? filters.sortField;
        filters.sortDirection = nextFilters.sortDirection ?? filters.sortDirection;
    }

    let rawFilterValue = '';
    if (typeof filters.filterValue === 'string') rawFilterValue = filters.filterValue.trim();
    else if (typeof filters.filterValue === 'number') rawFilterValue = String(filters.filterValue);

    let rawSearch = '';
    if (typeof filters.search === 'string') {
        rawSearch = filters.search.trim();
    }

    let nameFilter = undefined;
    if (filters.filterField === 'name') {
        nameFilter = rawFilterValue !== '' ? rawFilterValue : undefined;
    }

    router.get(
        route('products.index'),
        {
            page: 1,
            name: nameFilter,
            search: rawSearch !== '' ? rawSearch : undefined,
            sortField: filters.sortField,
            sortDirection: filters.sortDirection,
        },
        { preserveState: false, preserveScroll: true, replace: true },
    );
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
    router.get(route('products.create'));
};

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    loadProduct(id);
};

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    loadProduct(id);
};

const openModalEdit = (id) => {
    context.value = 'edit';
    showModal.value = true;
    loadProduct(id);
};

const closeModal = () => {
    showModal.value = false;
};

const deleteProduct = (id) => {
    form.delete(route('products.destroy', id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

const loadProduct = (id) => {
    window.axios
        .get(route('products.show', id), {
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            product.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const context = ref('show');

const pageTitle = 'Produtos';

const product = ref({});
</script>
