<template>
    <ExternalHeader/>
    <FormCreatePassword 
        v-if="hasToken"
        :user="user" 
        :token="token" 
        :formTitle="formTitle" />
    <Verified v-else-if="verified" />
</template>

<script setup>
import FormCreatePassword from './FormCreatePassword.vue';
import ExternalHeader from '../../Components/ExternalHeader.vue';
import Verified from './Verified.vue';

const props = defineProps({
    user: Object,
    token: String, 
    verified: Boolean,
    hasToken: Boolean
});

const pageTitle = 'Criar Senha'; 
const pageType = 'Autenticação';    
const formTitle = 'Criação de Senha'; 

</script>