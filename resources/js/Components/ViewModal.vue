<template>
    <Teleport to="body">
    <transition name="fade">
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center pt-32"
      >
        <!-- backdrop -->
        <div
          class="absolute inset-0 bg-black/40"
          @click="closeOnBackdrop && close()"
        />

        <!-- container -->
        <div
          class="relative -inset-y-1/12 bg-white rounded-lg shadow-lg mx-4 overflow-y-auto"
          :class="widths[maxWidth] ?? widths['2xl']"
        >
          <!-- botão fechar -->
          <button
            type="button"
            class="absolute top-3 right-3 text-red-600 hover:text-red-800 cursor-pointer"
            @click="close"
          >
            <font-awesome-icon icon="fa-solid fa-xmark" />
          </button>

          <!-- título -->
          <div v-if="$slots.title" class="p-10 pb-2">
            <slot name="title" />
          </div>

          <hr v-if="$slots.title" class="text-gray-300">

          <!-- conteúdo -->
          <div class="p-4">
            <slot />
          </div>

          <!-- footer -->
          <div v-if="$slots.footer" class="px-4 py-3 bg-gray-50 text-right">
            <slot name="footer" />
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>

import { onMounted, onBeforeUnmount } from 'vue';

const emit = defineEmits(['update:modelValue', 'close']);

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    closeOnEsc: { type: Boolean, default: true },
    closeOnBackDrop: { type: Boolean, default: true },
    maxWidth: { type: String, default: '2xl' },
    pageTitle: { type: String, default: 'Título' },
});

const close = () => {
    emit('update:modelValue', false);
    emit('close');
}

const widths = {
    sm: 'w-sm',
    md: 'w-md',
    lg: 'w-lg',
    xl: 'w-xl',
    '2xl': 'w-2xl',
    '50%': 'w-[50%]',
    '70%': 'w-[70%]',
    '90%': 'w-[90%]',
}

const onKeyDown = (e) => {
     if (e.key === 'Escape' && props.closeOnEsc && props.modelValue)
        close();
}

onMounted(() => window.addEventListener('keydown', onKeyDown))
onBeforeUnmount(() => window.removeEventListener('keydown', onKeyDown))
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
