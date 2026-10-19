<template>
    <div class="p-6">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Detalhes do recurso</h4>
                <p class="text-sm text-gray-500 mt-1">Visualize as informações do recurso selecionado.</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Nome do recurso</div>
                <div class="mt-1 text-base font-semibold text-gray-900 wrap-break-word">
                    {{ resource?.resourceName ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Código patrimonial</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ resource?.patrimonialCode ?? '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Categoria</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ categoryLabel }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Departamento</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ departmentLabel }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Fabricante</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ resource?.manufacturer || '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Modelo</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ resource?.model || '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Número de série</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ resource?.serialNumber || '-' }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Responsável (ID)</div>
                <div class="mt-1 text-sm text-gray-700 wrap-break-word">
                    {{ responsibleDisplay }}
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Link de documentação</div>
                <div class="mt-1 text-sm wrap-break-word">
                    <template v-if="documentationHref !== ''">
                        <a
                            class="text-blue-600 hover:underline"
                            :href="documentationHref"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            {{ documentationHref }}
                        </a>
                    </template>
                    <span v-else>-</span>
                </div>
            </div>
            <div class="rounded-xl border border-gray-100 bg-gray-50 p-4 md:col-span-2">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-bold">Observações</div>
                <div class="mt-1 text-sm text-gray-700 whitespace-pre-wrap wrap-break-word">
                    {{ resource?.notes || '-' }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatDocumentationUrl } from '@/helpers/masks';

const props = defineProps({
    resource: {
        type: Object,
        default: () => ({}),
    },
    categorySelectOptions: {
        type: Array,
        default: () => [],
    },
    departmentSelectOptions: {
        type: Array,
        default: () => [],
    },
});

const resolveOptionLabel = (options, id) => {
    if (!id || !options || options.length === 0) {
        return '-';
    }
    const sid = String(id);
    for (let i = 0; i < options.length; i++) {
        if (String(options[i].id) === sid) {
            return options[i].label;
        }
    }
    return '-';
};

const categoryLabel = computed(() => {
    return resolveOptionLabel(props.categorySelectOptions, props.resource?.categoryId);
});

const departmentLabel = computed(() => {
    return resolveOptionLabel(props.departmentSelectOptions, props.resource?.departmentId);
});

const responsibleDisplay = computed(() => {
    const rid = props.resource?.responsibleId;
    if (rid === undefined || rid === null) {
        return '-';
    }
    return String(rid);
});

const documentationHref = computed(() => {
    const raw = props.resource?.documentationLink;
    if (!raw) {
        return '';
    }
    return formatDocumentationUrl(String(raw));
});
</script>
