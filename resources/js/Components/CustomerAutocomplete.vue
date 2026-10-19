<template>
    <div class="relative">
        <label v-if="label" :for="inputId" class="input-label">{{ label }}</label>
        <div class="flex gap-2 items-start">
            <div class="flex-1 relative">
                <input
                    :id="inputId"
                    :value="query"
                    type="text"
                    class="input-text w-full"
                    :class="{ 'opacity-60': disabled }"
                    :disabled="disabled"
                    autocomplete="off"
                    role="combobox"
                    :aria-expanded="open"
                    aria-autocomplete="list"
                    @focus="onFocus"
                    @input="onInputChange"
                    @keydown="onInputKeydown"
                />
                <ul
                    v-if="open && filteredOptions.length > 0"
                    class="absolute z-50 mt-1 max-h-56 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg text-sm"
                    role="listbox"
                >
                    <li
                        v-for="(opt, idx) in filteredOptions"
                        :key="opt.id"
                        role="option"
                        :aria-selected="idx === highlightIndex"
                        class="px-3 py-2 cursor-pointer hover:bg-yellow-50"
                        :class="{ 'bg-yellow-100': idx === highlightIndex }"
                        @mousedown.prevent="selectOption(opt)"
                    >
                        {{ displayCompanyName(opt) }}
                    </li>
                </ul>
            </div>
            <button
                v-if="!disabled && modelValue"
                type="button"
                class="btn-submit shrink-0 bg-gray-600 px-3 py-2 text-sm mt-0"
                title="Limpar cliente"
                @click="clearSelection"
            >
                Limpar
            </button>
        </div>
        <div v-if="errorText" class="input-error mt-1">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
            {{ errorText }}
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    inputId: {
        type: String,
        default: 'customer-autocomplete',
    },
    label: {
        type: String,
        default: '',
    },
    errorText: {
        type: String,
        default: '',
    },
    /** Shown when id is set but missing from options (e.g. stale list). */
    fallbackDisplayName: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const query = ref('');
const highlightIndex = ref(-1);

function displayCompanyName(opt) {
    if (!opt || opt.company_name == null) {
        return '—';
    }
    return String(opt.company_name);
}

const labelFromOptionsForId = (id) => {
    if (!id) {
        return '';
    }
    for (let i = 0; i < props.options.length; i++) {
        if (props.options[i].id === id) {
            return displayCompanyName(props.options[i]);
        }
    }
    return '';
};

const resolveLabelForSelectedId = (id) => {
    let display = labelFromOptionsForId(id);
    if (display !== '' && display !== '—') {
        return display;
    }
    if (props.fallbackDisplayName && String(props.fallbackDisplayName).trim() !== '') {
        return String(props.fallbackDisplayName).trim();
    }
    return '';
};

watch(
    () => [props.modelValue, props.options, props.fallbackDisplayName],
    () => {
        const id = props.modelValue;
        if (!id) {
            return;
        }
        const display = resolveLabelForSelectedId(id);
        if (display !== '') {
            query.value = display;
        }
    },
    { immediate: true, deep: true },
);

const normalizedQuery = computed(() => {
    return query.value.trim().toLowerCase();
});

const filteredOptions = computed(() => {
    const q = normalizedQuery.value;
    const all = props.options;
    if (q === '') {
        return all.slice();
    }
    const out = [];
    for (let i = 0; i < all.length; i++) {
        const opt = all[i];
        let label = '';
        if (opt.company_name != null) {
            label = String(opt.company_name).toLowerCase();
        }
        if (label.includes(q)) {
            out.push(opt);
        }
    }
    return out;
});

const onFocus = () => {
    if (props.disabled) {
        return;
    }
    open.value = true;
    highlightIndex.value = -1;
};

const onInputChange = (e) => {
    const next = e.target.value;
    query.value = next;
    if (props.disabled) {
        return;
    }
    open.value = true;
    highlightIndex.value = -1;
    if (props.modelValue) {
        emit('update:modelValue', '');
    }
};

const selectOption = (opt) => {
    emit('update:modelValue', opt.id);
    let src = displayCompanyName(opt);
    if (src === '—') {
        src = '';
    }
    query.value = src;
    open.value = false;
    highlightIndex.value = -1;
};

const clearSelection = () => {
    emit('update:modelValue', '');
    query.value = '';
    open.value = false;
    highlightIndex.value = -1;
};

const moveHighlight = (delta) => {
    const list = filteredOptions.value;
    if (list.length === 0) {
        return;
    }
    let next = highlightIndex.value + delta;
    if (next < 0) {
        next = list.length - 1;
    }
    if (next >= list.length) {
        next = 0;
    }
    highlightIndex.value = next;
};

const onInputKeydown = (e) => {
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (!open.value) {
            open.value = true;
        }
        moveHighlight(1);
        return;
    }
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (!open.value) {
            open.value = true;
        }
        moveHighlight(-1);
        return;
    }
    if (e.key === 'Enter') {
        if (open.value && highlightIndex.value >= 0) {
            const list = filteredOptions.value;
            const opt = list[highlightIndex.value];
            if (opt) {
                e.preventDefault();
                selectOption(opt);
            }
        }
        return;
    }
    if (e.key === 'Escape') {
        open.value = false;
        highlightIndex.value = -1;
    }
};
</script>
