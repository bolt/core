<template>
  <div class="listing-records"></div>
</template>

<script>
export default {
  name: 'ListingRecords',
  props: ['type', 'data'],
  created() {
    this.$store.dispatch('general/setType', this.type);
    this.$store.dispatch('listing/setRecords', this.data);
  },
};
</script>
