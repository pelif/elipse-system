<template>
    <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-user" class="mr-2" />
                Usuários Cadastrados
            </h4>

            <Link
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar Usuário"
                :href="route('users.create')">
                <font-awesome-icon icon="fa-solid fa-square-plus " />
            </Link>
        </div>
        <div v-if="users.data && users.data.length === 0" class="p-6 text-center">
            Nenhum usuário cadastrado.
        </div>
        <div
            v-if="page.flash.message"
            class="success-message">
                <font-awesome-icon icon="fa-solid fa-check" class="mr-2 text-2xl" />
                {{ page.flash.message }}
         </div>
        <div
            v-if="page.flash.error"
            class="error-message">
                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                {{ page.flash.error }}
        </div>

        <filter-bar
            :filters="filters"
            :filterFields="[
                { key: 'name', label: 'Name' },
                { key: 'email', label: 'Email' },
                { key: 'role', label: 'Role' },
                { key: 'createdAt', label: 'Data de Criação' },
            ]"
            :searchFields="[]"
            @applyFilters="applyFilters"
            @clearFilters="clearFilters"
        />
        <table v-if="users.data && users.data.length > 0" class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-xs uppercase text-gray-400 font-bold">
                <tr>
                    <th class="px-6 py-4 w-[40%]">Nome</th>
                    <th class="px-6 py-4 w-[30%]">Email</th>
                    <th class="px-6 py-4 w-[15%]">Perfil</th>
                    <th class="px-6 py-4 w-[15%]">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in users.data" :key="user.id" class="hover:bg-yellow-100 transition-colors text-gray-500">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded flex items-center justify-center text-white font-bold text-xs"
                                :class="randomBg()">
                                {{ printInitials(user.name)  }}
                            </div>
                            <span class="font-medium text-gray-900 px-4 text-sm">{{ user.name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ user.email }}</td>
                    <td class="px-6 py-4 text-sm">{{ user.role }}</td>
                    <td class="px-6 py-4">
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-blue-500 cursor-pointer"
                            title="Visualizar"
                            @click="openModalShow(user.id)">
                            <font-awesome-icon icon="fa-solid fa-eye" />
                        </button>
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-orange-500 cursor-pointer"
                            title="Editar"
                            @click="editUser(user.id)" >
                            <font-awesome-icon icon="fa-solid fa-file-pen" />
                        </button>
                        <button
                            class="text-xl font-semibold text-elipse-gradient hover:opacity-80 px-2 text-red-800 cursor-pointer"
                            title="Excluir"
                            @click="openConfirmDelete(user.id)">
                            <font-awesome-icon icon="fa-solid fa-trash-can" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination -->
        <div v-if="users.meta && users.meta.last_page > 1" class="px-6 py-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-500">
                Mostrando <span class="font-medium text-gray-900">{{ users.meta.current_page }}</span> de <span class="font-medium text-gray-900">{{ users.meta.last_page }}</span> páginas
            </div>
            <div class="flex flex-wrap items-center gap-1">
                <template v-for="(link, index) in users.meta.links" :key="index">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="px-4 py-2 text-sm rounded-lg border font-medium transition-all duration-200"
                        :class="[
                            link.active
                                ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-transparent shadow-md hover:shadow-lg'
                                : 'text-gray-600 bg-white border-gray-200 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 shadow-sm'
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
        :pageTitle="ViewPageTitle"
    >
        <Show v-if="context === 'show'" :user="user" />
        <ConfirmDelete
            v-else
            :user="user"
            @close="closeModal"
            @delete="deleteUser"
        />
    </ViewModal>

</template>

<script setup>

import { computed, reactive, ref, watch } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import ViewModal from '@/Components/ViewModal.vue';
import FilterBar from '@/Components/FilterBar.vue';
import Show from './Show.vue';
import { randomBg, printInitials } from '@/helpers/styles';
import ConfirmDelete from './ConfirmDelete.vue';

const page = usePage();

const props = defineProps({
    users: {
        type: Object,
        default: () => ({ data: [], meta: {}, filters: {} })
    }
});

const initialFilters = computed(() => props.users?.filters ?? {});

const resolveInitialFilterField = (next) => {
    const order = ['name', 'email', 'role', 'createdAt'];
    for (const key of order) {
        if (next && typeof next[key] === 'string' && next[key].trim() !== '') {
            return key;
        }
    }
    return 'name';
};

const resolveInitialFilterValue = (next) => {
    const field = resolveInitialFilterField(next);
    return next?.[field] ?? '';
};

const filters = reactive({
    filterField: resolveInitialFilterField(initialFilters.value),
    filterValue: resolveInitialFilterValue(initialFilters.value),
    search: '',
    sortField: initialFilters.value.sortField ?? 'name',
    sortDirection: initialFilters.value.sortDirection ?? 'asc',
});

watch(
    () => props.users?.filters,
    (next) => {
        if (!next) return;

        filters.filterField = resolveInitialFilterField(next);
        filters.filterValue = resolveInitialFilterValue(next);
        filters.sortField = next.sortField ?? 'name';
        filters.sortDirection = next.sortDirection ?? 'asc';
    }
);

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
    }

    const query = {
        page: 1,
        name: undefined,
        email: undefined,
        role: undefined,
        createdAt: undefined,
        sortField: filters.sortField,
        sortDirection: filters.sortDirection,
    };

    if (rawFilterValue !== '') {
        query[filters.filterField] = rawFilterValue;
    }

    router.get(route('users.index'), query, {
        preserveState: false,
        preserveScroll: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.filterField = 'name';
    filters.filterValue = '';
    filters.sortField = 'name';
    filters.sortDirection = 'asc';
    applyFilters();
};

const user = ref({});

const showModal = ref(false);

const newUser = () => {
    router.get(route('users.create'));
}

const showUser = (id) => {
    window.axios.get(route('users.show', id))
        .then(response => {
            user.value = response.data;
        })
        .catch(error => {
            console.log(error);
        });
}

const openModalShow = (id) => {
    context.value = 'show';
    showModal.value = true;
    showUser(id);
}

const openConfirmDelete = (id) => {
    context.value = 'delete';
    showModal.value = true;
    showUser(id);
}

const closeModal = () => {
    showModal.value = false;
}

const form = useForm({});

const deleteUser = (id) => {
   form.delete(route('users.destroy', id), {
    preserveState: false,
    onSuccess: () => {
        closeModal();
    }
   });
}

const editUser = (id) => {
    router.get(route('users.edit', id));
}

const ViewPageTitle = ref('Visualização de Usuários');
const context = ref('show');

</script>
