<template>
    <div class="listing__records">
        <draggable v-model="records" handle=".listing__row--move" force-fallback="true" item-key="id">
            <template #item="{ element: record }">
                <table-row :record="record" :labels="labels"></table-row>
            </template>
        </draggable>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import draggable from 'vuedraggable';
import TableRow from './Row/index.vue';
import { useListingStore } from '../../store';
import type { ListingRecord } from '../../types';

defineProps<{
    labels: Record<string, string>;
}>();

const listingStore = useListingStore();

const records = computed({
    get() {
        return listingStore.records;
    },
    set(val: ListingRecord[]) {
        listingStore.records = val;
    },
});
</script>
