<template>
    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
        <h4 class="font-bold text-gray-700 text-2xl">{{ title }}</h4>
    </div>
    <p class="p-6 text-lg">
        Tem certeza que deseja excluir o recurso
        <strong>{{ resource?.resourceName }}</strong
        >?
    </p>
    <hr class="my-2 bg-gray-100" />
    <button type="button" class="btn-submit" @click="confirmDelete">Confirmar</button>
    <button type="button" class="btn-submit" @click="closeModal">Cancelar</button>
</template>

<script setup>
const props = defineProps({
    title: {
        type: String,
        default: 'Confirmação de exclusão',
    },
    resource: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['delete', 'close']);

const confirmDelete = () => {
    emit('delete', props.resource.id);
};

const closeModal = () => {
    emit('close');
};
</script>
