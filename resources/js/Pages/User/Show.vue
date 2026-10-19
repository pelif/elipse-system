<template #title>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes do Usuário</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações do usuário selecionado.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">E-mail</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ user.name ?? '-' }}
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 mt-2">
            <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Departamento</div>
            <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                {{ user.email ?? '-' }}
            </div>
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    user: {
        type: Object,
        default: () => ({})
    },
    Title: {
        type: String,
        default: 'Visualização de Usuários'
    }
});

</script>
