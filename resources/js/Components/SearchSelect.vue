<template>
    <div ref="rootEl" class="relative">
        <label v-if="label" :for="inputId" class="input-label">{{ label }}</label>
        <div class="flex gap-2 items-start">
            <div class="flex-1 relative">
                <input
                    :id="inputId"
                    :value="query"
                    type="text"
                    :class="inputClass"
                    :disabled="disabled"
                    :placeholder="placeholder"
                    autocomplete="off"
                    role="combobox"
                    :aria-expanded="open"
                    aria-autocomplete="list"
                    @focus="onFocus"
                    @input="onInputChange"
                    @keydown="onInputKeydown"
                />
                <ul
                    v-if="open && options.length > 0"
                    class="absolute z-50 mt-1 max-h-56 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg text-sm"
                    role="listbox"
                >
                    <li
                        v-for="(opt, idx) in options"
                        :key="opt.id"
                        role="option"
                        :aria-selected="idx === highlightIndex"
                        class="px-3 py-2 cursor-pointer hover:bg-yellow-50"
                        :class="optionClass(idx)"
                        @mousedown.prevent="selectOption(opt)"
                    >
                        {{ opt.label }}
                    </li>
                </ul>
            </div>
            <button
                v-if="showClear"
                type="button"
                class="btn-submit shrink-0 bg-gray-600 px-3 py-2 text-sm mt-0"
                title="Limpar"
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
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    endpoint: {
        type: String,
        required: true,
    },
    extraParams: {
        type: Object,
        default: () => ({}),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    inputId: {
        type: String,
        default: 'search-select',
    },
    label: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Digite para buscar',
    },
    errorText: {
        type: String,
        default: '',
    },
    selectedLabel: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'selected']);

const rootEl = ref(null);
const open = ref(false);
const query = ref('');
const options = ref([]);
const highlightIndex = ref(-1);
let requestSerial = 0;
let debounceTimer = null;

const inputClass = computed(() => {
    if (props.disabled) {
        return 'input-text w-full opacity-60';
    }

    return 'input-text w-full';
});

const showClear = computed(() => {
    if (props.disabled) {
        return false;
    }

    return props.modelValue !== '';
});

const optionClass = (idx) => {
    if (idx === highlightIndex.value) {
        return 'bg-yellow-100';
    }

    return '';
};

watch(
    () => [props.modelValue, props.selectedLabel],
    () => {
        if (!props.modelValue) {
            return;
        }
        if (props.selectedLabel) {
            query.value = props.selectedLabel;
        }
    },
    { immediate: true },
);

const fetchOptions = () => {
    if (props.disabled || props.endpoint === '') {
        options.value = [];
        return;
    }

    const params = {
        q: query.value,
    };
    const extra = props.extraParams;
    if (extra && typeof extra === 'object') {
        Object.keys(extra).forEach((key) => {
            params[key] = extra[key];
        });
    }

    const current = ++requestSerial;
    window.axios
        .get(props.endpoint, {
            params,
            headers: { Accept: 'application/json' },
        })
        .then((response) => {
            if (current !== requestSerial) {
                return;
            }
            if (Array.isArray(response.data)) {
                options.value = response.data;
                return;
            }
            options.value = [];
        })
        .catch(() => {
            if (current !== requestSerial) {
                return;
            }
            options.value = [];
        });
};

const scheduleFetch = () => {
    if (debounceTimer) {
        clearTimeout(debounceTimer);
    }
    debounceTimer = setTimeout(() => {
        fetchOptions();
    }, 250);
};

const closeDropdown = () => {
    open.value = false;
    highlightIndex.value = -1;
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

const onFocus = () => {
    if (props.disabled) {
        return;
    }
    open.value = true;
    highlightIndex.value = -1;
    fetchOptions();
};

const onInputChange = (event) => {
    query.value = event.target.value;
    if (props.disabled) {
        return;
    }
    open.value = true;
    highlightIndex.value = -1;
    if (props.modelValue) {
        emit('update:modelValue', '');
        emit('selected', null);
    }
    scheduleFetch();
};

const selectOption = (opt) => {
    emit('update:modelValue', opt.id);
    emit('selected', opt);
    query.value = opt.label;
    open.value = false;
    highlightIndex.value = -1;
};

const clearSelection = () => {
    emit('update:modelValue', '');
    emit('selected', null);
    query.value = '';
    open.value = false;
    highlightIndex.value = -1;
    options.value = [];
};

const moveHighlight = (delta) => {
    const list = options.value;
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

const onInputKeydown = (event) => {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        if (!open.value) {
            open.value = true;
            fetchOptions();
        }
        moveHighlight(1);
        return;
    }
    if (event.key === 'ArrowUp') {
        event.preventDefault();
        if (!open.value) {
            open.value = true;
        }
        moveHighlight(-1);
        return;
    }
    if (event.key === 'Enter') {
        if (open.value && highlightIndex.value >= 0) {
            const opt = options.value[highlightIndex.value];
            if (opt) {
                event.preventDefault();
                selectOption(opt);
            }
        }
        return;
    }
    if (event.key === 'Escape') {
        closeDropdown();
    }
};
</script>
