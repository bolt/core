<template>
  
</template>

<script>
export default {
  name: "listing-records",
  props: ["type", "data"],
  created(){
    this.$store.dispatch('general/setType', this.type);
    this.$store.dispatch('listing/setRecords', this.data);
  }
};
</script>