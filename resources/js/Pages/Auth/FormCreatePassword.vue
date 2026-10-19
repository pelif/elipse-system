<template>
    <div class="w-auto mx-4 overflow-auto bg-white rounded-lg shadow-md p-4 mt-4 border border-gray-100">
        <div class="p-6 border-b border-gray-50 flex justify-between items-center">
            <h4 class="font-bold text-gray-700 text-2xl">
                <font-awesome-icon icon="fa-solid fa-key" class="mr-2" />       
                {{ formTitle }}
            </h4>
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
                <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2 text-2xl" />
                {{ page.flash.error }}
            </div>
            <div class="container-form">
                <div class="group-form">
                    <div class="input-container">
                        <label for="password" class="input-label">Senha</label>
                        <input type="password" name="password" v-model="form.password" class="input-text">
                        <div v-if="form.errors.password" class="input-error">
                            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                            {{ form.errors.password }}
                        </div>
                    </div>
                    <div class="input-container">
                        <label for="password_confirmation" class="input-label">Confirmação de Senha</label>
                        <input type="password" name="password_confirmation" v-model="form.password_confirmation" class="input-text">
                        <div v-if="form.errors.password_confirmation" class="input-error">
                            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>
                </div>
                <div class="group-form"></div>
                <div class="group-form">
                    <div class="group-form mb-2 flex justify-end">
                        <button type="button" class="btn-submit mr-2">Cancelar</button>
                        <button type="submit" class="btn-submit" @click="submit" :disabled="form.processing">
                            Salvar Senha
                        </button>
                    </div>
                </div>
            </div>
        </form>       
    </div>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage(); 

const props = defineProps({
    user: Object,
    token: String,
    formTitle: String
});

const form = useForm({
    id: props.user?.id,
    name: props.user?.name,
    email: props.user?.email,
    password: '',
    password_confirmation: '',
    token: props.token
}); 

const submit = () => {
    if (props.user?.id) 
        form.put(route('password.update', props.user.id)); 
}; 
    
    

</script>