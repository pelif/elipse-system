<template>
    <div class="p-6">
        <p class="text-gray-700">
            Confirma a exclusão desta configuração de e-mail?
        </p>

        <div class="mt-6 flex justify-end gap-2">
            <button type="button" class="btn-submit bg-gray-700" @click="$emit('close')">
                Cancelar
            </button>
            <button type="button" class="btn-submit bg-red-700" @click="$emit('delete', emailConfig.id)">
                Excluir
            </button>
        </div>
    </div>
</template>

<script setup>
defineProps({
    emailConfig: { type: Object, required: true },
});
</script>

