<template>
    <div class="listing-records"></div>
</template>

<script>
import store from '../store';

export default {
    name: 'ListingRecords',
    props: {
        type: String,
        data: Array,
        labels: Object,
    },
    created() {
        store.dispatch('general/setType', this.type);
        store.dispatch('listing/setRecords', this.data);
    },
};
</script>
