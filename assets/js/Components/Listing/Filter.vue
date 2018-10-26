<template>
  <ul class="listing__filter--controls">
    <li>
      <button class="control--button" :class="{'is-selected': size === 'small'}" @click="changeSize('small')">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 55"><g fill-rule="nonzero"><rect width="70" height="10" rx="3"/><rect width="70" height="10" rx="3" transform="translate(0 15)"/><rect width="70" height="10" rx="3" transform="translate(0 30)"/><rect width="70" height="10" rx="3" transform="translate(0 45)"/></g></svg>
      </button>
    </li>
    <li class="mr-3">
      <button class="control--button" :class="{'is-selected': size === 'normal'}" @click="changeSize('normal')">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 55"><g fill-rule="nonzero"><rect width="70" height="15" rx="3"/><rect width="70" height="15" rx="3" transform="translate(0 20)"/><rect width="70" height="15" rx="3" transform="translate(0 40)"/></g></svg>
      </button>
    </li>
    <li>
      <button class="control--button" :class="{'is-active': sorting}" @click="sortRows()">
        <i class="fas fa-sort"></i>  
      </button>
    </li>
  </ul>
</template>

<script>
module.exports = {
  name: "listing-filter",
  mounted() {
    const size = localStorage.getItem('listing-row-size');
    if (size !== null)
      this.$root.$emit('listing-row-size', size);
      this.size = size
  },
  data: () => {
    return {
      size: "normal",
      sorting: false
    };
  },
  methods:{
    sortRows(){
      if(this.sorting === false){
        this.sorting = true
        this.$root.$emit('listing-row-sorting', true);
      } else {
        this.sorting = false
        this.$root.$emit('listing-row-sorting', false);
      }
    },
    changeSize(size){
      this.$root.$emit('listing-row-size', size);
      localStorage.setItem('listing-row-size', size);
      this.size = size
    }
  }
};
</script>