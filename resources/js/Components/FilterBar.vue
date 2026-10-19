<template>
  <div class="p-6 border-b border-gray-50">
    <div class="flex flex-nowrap items-end gap-2 overflow-x-auto">
      <div class="flex flex-col gap-1 shrink-0">
        <label class="text-xs uppercase text-gray-400 font-bold block"
          >Filtrar por</label
        >
        <select
          v-model="filters.filterField"
          class="input-text w-36 focus:outline-none focus:ring-0 focus:border-gray-200"
        >
          <option
            v-for="field in normalizedFilterFields"
            :key="field.key"
            :value="field.key"
          >
            {{ field.label }}
          </option>
        </select>
      </div>

      <div class="flex flex-col gap-1 shrink-0">
        <label class="text-xs uppercase text-gray-400 font-bold block"
          >Valor</label
        >
        <input
          v-model="filters.filterValue"
          type="text"
          class="input-text w-36 focus:outline-none focus:ring-0 focus:border-gray-200"
          :placeholder="filterValuePlaceholder"
          @keyup.enter="applyFilters"
        />
      </div>

      <div v-if="showSearch" class="flex flex-col gap-1 shrink-0">
        <label class="text-xs uppercase text-gray-400 font-bold block"
          >Busca</label
        >
        <input
          v-model="filters.search"
          type="text"
          class="input-text w-36 focus:outline-none focus:ring-0 focus:border-gray-200"
          placeholder="Buscar..."
          @keyup.enter="applyFilters"
        />
      </div>

      <div v-if="showSort" class="flex flex-col gap-1 shrink-0">
        <label class="text-xs uppercase text-gray-400 font-bold block"
          >Ordenar por</label
        >
        <select
          v-model="filters.sortField"
          class="input-text w-36 focus:outline-none focus:ring-0 focus:border-gray-200"
        >
          <option
            v-for="field in normalizedSortFields"
            :key="field.key"
            :value="field.key"
          >
            {{ field.label }}
          </option>
        </select>
      </div>

      <div class="flex gap-2 items-end shrink-0">
        <div v-if="showSort" class="flex flex-col gap-1 shrink-0">
          <label class="text-xs uppercase text-gray-400 font-bold block"
            >Direção</label
          >
          <select
            v-model="filters.sortDirection"
            class="input-text w-36 focus:outline-none focus:ring-0 focus:border-gray-200"
          >
            <option value="asc">Crescente</option>
            <option value="desc">Decrescente</option>
          </select>
        </div>
        <button
          type="button"
          class="btn-submit h-10 shrink-0 py-2 focus:outline-none focus:ring-0"
          @click="applyFilters"
        >
          Filtrar
        </button>
        <button
          type="button"
          class="btn-submit h-10 shrink-0 py-2 bg-gray-700 focus:outline-none focus:ring-0"
          @click="clearFilters"
        >
          Limpar
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({
      filterField: "id",
      filterValue: "",
      search: "",
      sortField: "id",
      sortDirection: "asc",
    }),
  },
  filterFields: {
    type: Array,
    default: () => [],
  },
  searchFields: {
    type: Array,
    default: () => [],
  },
  /** When set, options for "Ordenar por"; otherwise the same as filter fields. */
  sortFields: {
    type: Array,
    default: null,
  },
  showSearch: {
    type: Boolean,
    default: true,
  },
  showSort: {
    type: Boolean,
    default: true,
  },
});

const emit = defineEmits(["applyFilters", "clearFilters"]);

const filters = props.filters;

const normalizedFilterFields = computed(() =>
  (Array.isArray(props.filterFields) ? props.filterFields : []).filter(
    (f) => f && typeof f.key === "string" && typeof f.label === "string"
  )
);

const normalizedSortFields = computed(() => {
  const fromProp = Array.isArray(props.sortFields) ? props.sortFields : null;
  const source = fromProp && fromProp.length > 0 ? fromProp : props.filterFields;
  return (Array.isArray(source) ? source : []).filter(
    (f) => f && typeof f.key === "string" && typeof f.label === "string"
  );
});

const filterValuePlaceholder = computed(() => {
  const current = normalizedFilterFields.value.find(
    (f) => f.key === filters.filterField
  );
  const label = current?.label ?? "campo";
  return `Filtrar por ${label}`;
});

const showSearch = computed(() => {
  if (!props.showSearch) return false;
  return Array.isArray(props.searchFields)
    ? props.searchFields.length > 0
    : true;
});

const showSort = computed(() => props.showSort);

const applyFilters = () => {
  emit("applyFilters", { ...filters });
};

const clearFilters = () => {
  emit("clearFilters");
};
</script>
