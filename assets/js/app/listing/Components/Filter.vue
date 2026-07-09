<template>
    <nav class="listing__filter">
        <ul class="listing__filter--controls">
            <!-- Check all checkbox -->
            <li v-if="type !== 'dashboard'" class="control--checkbox">
                <div v-if="!sorting" class="form-check">
                    <input
                        id="selectAll"
                        :value="selectAll"
                        class="form-check-input"
                        type="checkbox"
                        @click="enableSelectAll(!selectAll)"
                    />
                    <label class="form-check-label form-label" for="selectAll">
                        <span class="sr-only">{{ labels.select_all }}</span>
                    </label>
                </div>
            </li>

            <!-- Expanded mode -->
            <li class="control--left">
                <button
                    class="control--button"
                    :class="{ 'is-selected': size === 'normal' }"
                    :title="labels.button_expanded"
                    @click="changeSize('normal')"
                >
                    <i class="fas fa-align-justify fa-fw"></i>
                    <span class="sr-only">{{ labels.button_expanded }}</span>
                </button>
            </li>

            <!-- Compact mode -->
            <li>
                <button
                    class="control--button"
                    :class="{ 'is-selected': size === 'small' }"
                    :title="labels.button_compact"
                    @click="changeSize('small')"
                >
                    <i class="fas fa-grip-lines fa-fw"></i>
                    <span class="sr-only">{{ labels.button_compact }}</span>
                </button>
            </li>

            <!-- Drag and drop -->
            <!--
      <li v-if="type !== 'dashboard'">
        <button
          class="control--button"
          :class="{ 'is-active': sorting }"
          @click="enableSorting(!sorting)"
        >
          <i class="fas" :class="sorting ? 'fa-check-circle' : 'fa-sort'"></i>
        </button>
      </li>
      -->
        </ul>
    </nav>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue';
import { useGeneralStore, useSelectingStore } from '../store';

defineProps<{
    labels: Record<string, string>;
}>();

const generalStore = useGeneralStore();
const selectingStore = useSelectingStore();

const type = computed(() => generalStore.type);
const size = computed(() => generalStore.rowSize);
const sorting = computed(() => generalStore.sorting);
const selectAll = computed(() => selectingStore.selectAll);

watch(sorting, newSorting => {
    if (newSorting) {
        selectingStore.selectAll = false;
    }
});

const sizeFromStorage = localStorage.getItem('listing-row-size');
if (sizeFromStorage !== null) {
    generalStore.rowSize = sizeFromStorage;
}

function enableSelectAll(arg: boolean) {
    selectingStore.selectAll = arg;
}

function changeSize(newSize: string) {
    generalStore.rowSize = newSize;
    localStorage.setItem('listing-row-size', newSize);
}
</script>
