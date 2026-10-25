<template>
    <div v-show="!sorting" key="checkbox" class="listing--checkbox">
        <div class="form-check">
            <input :id="`row-${id}`" v-model="selected" class="form-check-input" type="checkbox" />
            <label class="form-check-label" :for="`row-${id}`"></label>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { useSelectingStore, useGeneralStore } from '../../../store';

const props = defineProps<{
    id: number;
}>();

const selectingStore = useSelectingStore();
const generalStore = useGeneralStore();

const selected = ref(false);

const selectAll = computed(() => selectingStore.selectAll);
const sorting = computed(() => generalStore.sorting);

watch(selectAll, newSelectAll => {
    selected.value = newSelectAll;
});

watch(selected, newSelected => {
    if (newSelected) {
        selectingStore.select(props.id);
    } else {
        selectingStore.deSelect(props.id);
    }
});

watch(sorting, newSorting => {
    if (newSorting) {
        selected.value = false;
    }
});
</script>
