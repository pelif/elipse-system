<template>
    <div class="input-container">
        <label :for="inputId" class="input-label">{{ label }}</label>
        <select
            :id="inputId"
            class="input-text"
            :value="modelValue"
            @change="onChange"
        >
            <option value="">{{ placeholder }}</option>
            <option v-for="opt in options" :key="opt.id" :value="opt.id">{{ opt.label }}</option>
        </select>
        <div v-if="error" class="input-error">
            <font-awesome-icon icon="fa-solid fa-circle-exclamation" class="mr-2" />
            {{ error }}
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    inputId: {
        type: String,
        required: true,
    },
    modelValue: {
        type: String,
        default: '',
    },
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: '—',
    },
    error: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const onChange = (event) => {
    emit('update:modelValue', event.target.value);
};
</script>
