<script setup lang="ts">
import ExternalHeader from '../../Components/ExternalHeader.vue';
import FormUpdatePassword from '../../Components/Auth/FormUpdatePassword.vue';
import Verified from './Verified.vue';

const props = defineProps({
    user: Object,
    token: String,
    verified: Boolean,
    hasToken: Boolean
});

const formTitle = 'Alteração de Senha';

</script>

<template>
    <ExternalHeader />
    <FormUpdatePassword         
        v-if="hasToken && user"
        :user="user" 
        :token="token" 
        :formTitle="formTitle" />
    <Verified v-else-if="verified" />
</template>