<template>
  <div class="listing__filter--controls">
    <button class="btn btn-white" @click="changeSize('small')">Small</button>
    <button class="btn btn-white" @click="changeSize('normal')">Normal</button>
  </div>
</template>

<script>
module.exports = {
  name: "listing-filter",
  mounted() {
    const size = localStorage.getItem('listing-row-size');
    if (size !== null)
      this.$root.$emit('listing-row-size', size);
  },
  methods:{
    changeSize(size){
      this.$root.$emit('listing-row-size', size);
      localStorage.setItem('listing-row-size', size);
    }
  }
};
</script>