<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes da Configuração de E-mail</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações da configuração de e-mail selecionada.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">E-mail</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ emailConfig.address ?? '-' }}
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 mt-2">
            <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Departamento</div>
            <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                {{ departmentName ?? '-' }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    emailConfig: { type: Object, required: true },
    departments: { type: Array, default: () => [] },
});

const departmentName = computed(() => {
    const id = props.emailConfig.departmentId;
    const d = props.departments.find((x) => x.id === id);
    return d ? d.department : '-';
});
</script>

