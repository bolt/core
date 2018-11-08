<template>
  <button 
    v-if="sorting" 
    class="listing__row--move"
  >
    <i class="fas px-2 fa-equals"></i>
  </button>
</template>

<script>
  export default {
    name: "sorting",
    computed: {
      sorting() {
        return this.$store.getters['general/getSorting']
      }
    },
  };
</script>