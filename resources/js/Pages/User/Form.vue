<template>
    <div class="w-auto mx-4 my-6 overflow-x-auto bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-user" class="mr-2" />
                {{ title }}
            </h4>
            <Link
                v-if="props.user?.id"
                class="text-4xl font-semibold text-elipse-gradient hover:opacity-80 transition-all duration-300 ease-in-out cursor-pointer"
                title="Adicionar Usuário"
                :href="route('users.create')">
                <font-awesome-icon icon="fa-solid fa-square-plus " />
            </Link>
        </div>

        <form @submit.prevent="submit">
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
            <div class="container-form">
                <div class="group-form">
                    <div class="input-container">
                        <label for="name" class="input-label">Nome Completo</label>
                        <input
                            type="text"
                            name="name"
                            v-model="form.name"
                            class="input-text">
                        <div
                            v-if="form.errors.name"
                            class="input-error">
                            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                            {{ form.errors.name }}
                        </div>
                    </div>
                    <div class="input-container">
                        <label for="email" class="input-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            v-model="form.email"
                            class="input-text">
                        <div
                            v-if="form.errors.email"
                            class="input-error">
                            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                            {{ form.errors.email }}
                        </div>
                    </div>
                </div>

                <div class="group-form">
                    <div class="input-container">
                        <label for="role" class="input-label">Perfil</label>
                        <select
                            name="role"
                            v-model="form.role"
                            class="input-text">
                            <option
                                v-for="(role, index) in roles"
                                :key="index"
                                :value="role.label">
                                {{ role.label }}
                            </option>
                        </select>
                        <div
                            v-if="form.errors.role"
                            class="input-error">
                            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                            {{ form.errors.role }}
                        </div>
                    </div>
                </div>

                <div class="group-form"></div>

                <div class="group-form">
                    <div class="group-form mb-2 flex justify-end">
                        <button
                            v-if="props.user?.id"
                            type="button"
                            class="btn-submit mr-2"
                            @click.prevent="handleUpdatePassword(props.user.id)">
                            Mudar Senha
                        </button>
                        <button type="submit" class="btn-submit" :disabled="form.processing">
                            {{ props.user?.id ? 'Atualizar' : 'Salvar' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import { Link, useForm, usePage, router } from '@inertiajs/vue3';

const page = usePage();

const submit = () => {
    if (props.user?.id) {
        form.put(route('users.update', props.user.id));
    } else {
        form.post(route('users.store'));
    }
};

const handleUpdatePassword = (id) => {
    if (id) {
        router.post(route('users.new-password', id), {}, {
            preserveScroll: true
        });
    }
}

const props = defineProps({
    user: {
        type: Object,
        default: () => ({})
    },
    roles: {
        type: Array,
        default: () => []
    },
    title: {
        type: String,
        default: 'Novo Usuário'
    },

});

const form = useForm({
    id: props.user?.id,
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    password: '',
    password_confirmation: ''
});


</script>
