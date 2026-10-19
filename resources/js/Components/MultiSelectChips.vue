<template>
    <div ref="rootEl" class="relative">
        <label v-if="label" :for="inputId" class="input-label">{{ label }}</label>
        <div
            class="input-text min-h-11 h-auto flex flex-wrap gap-2 items-center cursor-text py-2"
            @click="openDropdown"
        >
            <span
                v-for="chip in selectedOptions"
                :key="chip.id"
                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-medium"
            >
                {{ chip.label }}
                <button
                    type="button"
                    class="text-amber-700 hover:text-amber-900 cursor-pointer leading-none"
                    :title="'Remover ' + chip.label"
                    @click.stop="removeId(chip.id)"
                >
                    <font-awesome-icon icon="fa-solid fa-xmark" />
                </button>
            </span>
            <input
                :id="inputId"
                v-model="query"
                type="text"
                class="flex-1 min-w-32 border-0 outline-none bg-transparent p-0 text-sm text-gray-700"
                autocomplete="off"
                :placeholder="placeholderText"
                @focus="openDropdown"
                @keydown.backspace="onBackspace"
                @keydown.escape="closeDropdown"
            />
        </div>
        <ul
            v-if="dropdownOpen && availableOptions.length > 0"
            class="absolute z-50 mt-1 max-h-56 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg text-sm"
            role="listbox"
        >
            <li
                v-for="opt in availableOptions"
                :key="opt.id"
                role="option"
                class="px-3 py-2 cursor-pointer hover:bg-yellow-50 text-gray-700"
                @mousedown.prevent="addId(opt.id)"
            >
                {{ opt.label }}
            </li>
        </ul>
        <div v-if="error" class="input-error">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
            {{ error }}
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    options: {
        type: Array,
        default: () => [],
    },
    label: {
        type: String,
        default: '',
    },
    inputId: {
        type: String,
        default: 'multi-select-chips',
    },
    error: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Selecione',
    },
});

const emit = defineEmits(['update:modelValue']);

const query = ref('');
const dropdownOpen = ref(false);
const rootEl = ref(null);

const selectedIds = computed(() => {
    if (!Array.isArray(props.modelValue)) {
        return [];
    }
    return props.modelValue.map((id) => String(id));
});

const optionById = computed(() => {
    const map = {};
    props.options.forEach((opt) => {
        map[String(opt.id)] = opt;
    });
    return map;
});

const selectedOptions = computed(() => {
    const items = [];
    selectedIds.value.forEach((id) => {
        const opt = optionById.value[id];
        if (opt) {
            items.push({
                id: String(opt.id),
                label: opt.label,
            });
        }
    });
    return items;
});

const availableOptions = computed(() => {
    const selected = {};
    selectedIds.value.forEach((id) => {
        selected[id] = true;
    });
    const search = query.value.trim().toLowerCase();
    const items = [];
    props.options.forEach((opt) => {
        const id = String(opt.id);
        if (selected[id]) {
            return;
        }
        const label = String(opt.label ?? '');
        if (search !== '' && !label.toLowerCase().includes(search)) {
            return;
        }
        items.push({
            id,
            label,
        });
    });
    return items;
});

const placeholderText = computed(() => {
    if (selectedIds.value.length > 0) {
        return '';
    }
    return props.placeholder;
});

const emitIds = (nextIds) => {
    emit('update:modelValue', nextIds);
};

const addId = (id) => {
    const next = selectedIds.value.slice();
    const stringId = String(id);
    if (next.includes(stringId)) {
        query.value = '';
        return;
    }
    next.push(stringId);
    emitIds(next);
    query.value = '';
};

const removeId = (id) => {
    const stringId = String(id);
    const next = selectedIds.value.filter((current) => current !== stringId);
    emitIds(next);
};

const openDropdown = () => {
    dropdownOpen.value = true;
};

const closeDropdown = () => {
    dropdownOpen.value = false;
};

const onBackspace = () => {
    if (query.value !== '') {
        return;
    }
    if (selectedIds.value.length === 0) {
        return;
    }
    const lastId = selectedIds.value[selectedIds.value.length - 1];
    removeId(lastId);
};

const onDocumentClick = (event) => {
    if (!rootEl.value) {
        return;
    }
    if (rootEl.value.contains(event.target)) {
        return;
    }
    closeDropdown();
};

onMounted(() => {
    document.addEventListener('click', onDocumentClick);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', onDocumentClick);
});
</script>
