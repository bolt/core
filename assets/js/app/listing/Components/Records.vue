<template>
    <div class="listing-records"></div>
</template>

<script>
import { useGeneralStore, useListingStore } from '../store';

export default {
    name: 'ListingRecords',
    props: {
        type: String,
        data: Array,
        labels: Object,
    },
    created() {
        useGeneralStore(this.$pinia).type = this.type;
        useListingStore(this.$pinia).records = this.data;
    },
};
</script>
