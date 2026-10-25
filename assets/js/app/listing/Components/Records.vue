<template>
    <div class="listing-records"></div>
</template>

<script setup lang="ts">
import { useGeneralStore, useListingStore } from '../store';
import type { ListingRecord } from '../types';

const props = defineProps<{
    type?: string;
    data?: ListingRecord[];
    labels?: Record<string, string>;
}>();

const generalStore = useGeneralStore();
const listingStore = useListingStore();

if (props.type) {
    generalStore.type = props.type;
}
if (props.data) {
    listingStore.records = props.data;
}
</script>
