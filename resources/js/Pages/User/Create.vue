<template>
    <MainLayout>
        <Header :pageTitle="pageTitle" :pageType="pageType" />
        <Form :user="user" :roles="roles" :title="formTitle" />
    </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import Header from '@/Components/Header.vue';
import Form from './Form.vue';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({})
    },
    roles: {
        type: Array,
        default: () => []
    }
});

const pageTitle = 'Novo Usuário';
const pageType = 'Configurações';
const formTitle = 'Novo Usuário';

</script>
