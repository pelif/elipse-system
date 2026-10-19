<template>
    <div
        class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100"
    >
        <div
            class="p-6 border-b border-gray-50 flex justify-between items-center"
        >
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-video" class="mr-2" />
                Fluxo de Jobs
            </h4>

            <button
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar Status de Job"
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
            <font-awesome-icon
                icon="fa-solid fa-circle-exclamation"
                class="mr-2"
            />
            {{ page.flash.error }}
        </div>

        <filter-bar
            :filters="filters"
            :filterFields="[
                { key: 'id', label: 'ID' },
                { key: 'status', label: 'Status' },
                { key: 'type', label: 'Tipo' },
                { key: 'observacoes', label: 'Observações' },
            ]"
            :searchFields="[
                { key: 'status', label: 'Status' },
                { key: 'type', label: 'Tipo' },
                { key: 'observacoes', label: 'Observações' },
            ]"
            @applyFilters="applyFilters"
            @clearFilters="clearFilters"
        />

        <div
            v-if="statuses?.data && statuses.data.length === 0"
            class="p-6 text-center"
        >
            Nenhum status de job cadastrado.
        </div>

        <table v-else class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4 w-[10%]">Status</th>
                    <th class="px-6 py-4 w-[8%]">Tipo</th>
                    <th class="px-6 py-4 w-[70%]">Observações</th>
                    <th class="px-6 py-4 w-[12%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="row in statuses.data"
                    :key="row.id"
                    class="hover:bg-yellow-100 transition-colors text-gray-500"
                >
                    <td
                        class="px-6 py-4 text-sm text-gray-900 font-medium"
                    >
                        {{ row.status }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium w-1/12">
                        {{ row.type === 1 ? 'Job' : 'Os' }}
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ row.observacoes ?? "-" }}
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
                    </td>
                </tr>
            </tbody>
        </table>

        <div
            v-if="statuses?.meta && statuses.meta.last_page > 1"
            class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4"
        >
            <div class="text-sm text-gray-500">
                Mostrando
                <span class="font-medium text-gray-900">{{
                    statuses.meta.current_page
                }}</span>
                de
                <span class="font-medium text-gray-900">{{
                    statuses.meta.last_page
                }}</span>
                páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template
                    v-for="(link, index) in statuses.meta.links"
                    :key="index"
                >
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
        :pageTitle="pageTitle"
    >
        <ShowContent v-if="context === 'show'" :status="status" />
        <EditContent
            v-else-if="context === 'edit'"
            :status="status"
            @close="closeModal"
        />
        <ConfirmDelete
            v-else
            :status="status"
            @close="closeModal"
            @delete="deleteStatus"
        />
    </ViewModal>
</template>

<script setup>
import { computed, reactive, ref, watch } from "vue";
import { Link, useForm, usePage, router } from "@inertiajs/vue3";
import ViewModal from "@/Components/ViewModal.vue";
import FilterBar from "@/Components/FilterBar.vue";
import ShowContent from "./ShowContent.vue";
import EditContent from "./EditContent.vue";
import ConfirmDelete from "./ConfirmDelete.vue";

const page = usePage();

const props = defineProps({
    statuses: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} }),
    },
});

const initialFilters = computed(() => props.statuses?.filters ?? {});

const filters = reactive({
    filterField: initialFilters.value.id
        ? "id"
        : initialFilters.value.status
          ? "status"
          : initialFilters.value.type
            ? "type"
            : initialFilters.value.observacoes
            ? "observacoes"
            : "id",
    filterValue:
        initialFilters.value.id ??
        initialFilters.value.status ??
        initialFilters.value.type ??
        initialFilters.value.observacoes ??
        "",
    search: initialFilters.value.search ?? "",
    sortField: initialFilters.value.sortField ?? "id",
    sortDirection: initialFilters.value.sortDirection ?? "asc",
});

watch(
    () => props.statuses?.filters,
    (next) => {
        if (!next) return;

        if (next.id)
            filters.filterField = "id";
        else if (next.status)
            filters.filterField = "status";
        else if (next.type)
            filters.filterField = "type";
        else if (next.observacoes)
            filters.filterField = "observacoes";
        else
            filters.filterField = "id";

        filters.filterValue = next.id ?? next.status ?? next.type ?? next.observacoes ?? "";
        filters.search = next.search ?? "";
        filters.sortField = next.sortField ?? "id";
        filters.sortDirection = next.sortDirection ?? "asc";
    },
);

const applyFilters = (nextFilters) => {
    if (nextFilters && typeof nextFilters === "object") {
        filters.filterField = nextFilters.filterField ?? filters.filterField;
        filters.filterValue = nextFilters.filterValue ?? filters.filterValue;
        filters.search = nextFilters.search ?? filters.search;
        filters.sortField = nextFilters.sortField ?? filters.sortField;
        filters.sortDirection = nextFilters.sortDirection ?? filters.sortDirection;
    }

    let rawFilterValue = "";
    if (typeof filters.filterValue === "string") rawFilterValue = filters.filterValue.trim();
    else if (typeof filters.filterValue === "number") rawFilterValue = String(filters.filterValue);
    else if (typeof filters.filterValue === "boolean") rawFilterValue = filters.filterValue ? "1" : "0";

    let rawSearch = "";
    if (typeof filters.search === "string") {
        rawSearch = filters.search.trim();
    }

    let id = undefined;
    if (filters.filterField === "id") {
        id = rawFilterValue !== "" ? rawFilterValue : undefined;
    }

    let status = undefined;
    if (filters.filterField === "status") {
        status = rawFilterValue !== "" ? rawFilterValue : undefined;
    }

    let type = undefined;
    if (filters.filterField === "type") {
        type = rawFilterValue !== "" ? rawFilterValue : undefined;
    }

    let observacoes = undefined;
    if (filters.filterField === "observacoes") {
        observacoes = rawFilterValue !== "" ? rawFilterValue : undefined;
    }

    router.get(
        route("job-flow.index"),
        {
            // Sempre volta para a 1ª página ao mudar filtros/ordenação/busca,
            // evitando ficar “preso” numa página alta e parecer que não filtrou.
            page: 1,
            id: id !== "" ? id : undefined,
            status: status !== "" ? status : undefined,
            type: type !== "" ? type : undefined,
            observacoes: observacoes !== "" ? observacoes : undefined,
            search: rawSearch !== "" ? rawSearch : undefined,
            sortField: filters.sortField,
            sortDirection: filters.sortDirection,
        },
        // Força recarregar os dados na navegação (URL + props),
        // evitando ficar com o resultado “antigo” preservado.
        { preserveState: false, preserveScroll: true, replace: true },
    );
};

const clearFilters = () => {
    filters.filterField = "id";
    filters.filterValue = "";
    filters.search = "";
    filters.sortField = "id";
    filters.sortDirection = "asc";
    applyFilters();
};

const showModal = ref(false);

const form = useForm({
    confirm: true,
});

const goToCreate = () => {
    router.get(route("job-flow.create"));
};

const openModalShow = (id) => {
    context.value = "show";
    showModal.value = true;
    showStatus(id);
};

const openConfirmDelete = (id) => {
    context.value = "delete";
    showModal.value = true;
    showStatus(id);
};

const openModalEdit = (id) => {
    context.value = "edit";
    showModal.value = true;
    showStatus(id);
};

const closeModal = () => {
    showModal.value = false;
};

const deleteStatus = (id) => {
    form.delete(route("job-flow.destroy", id), {
        preserveState: false,
        onSuccess: () => {
            closeModal();
        },
    });
};

const showStatus = (id) => {
    window.axios
        .get(route("job-flow.show", id))
        .then((response) => {
            status.value = response.data;
        })
        .catch((error) => {
            console.log(error);
        });
};

const context = ref("show");

const pageTitle = "Fluxo de Jobs";

const status = ref({});
</script>
