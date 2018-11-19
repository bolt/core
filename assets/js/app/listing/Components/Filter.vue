<template>
  <nav class="listing__filter">
    <ul class="listing__filter--controls">
      <li v-if="type !== 'dashboard'">
        <div class="custom-control custom-checkbox" v-if="!sorting">
            <input type="checkbox" class="custom-control-input" id="selectAll" v-model="selectAll">
            <label 
              class="custom-control-label" 
              for="selectll" 
              @click="enableSelectAll(!selectAll)"
            ></label>
        </div>
      </li>
      <li class="control--left">
        <button class="control--button" :class="{'is-selected': size === 'small'}" @click="changeSize('small')">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 55"><g fill-rule="nonzero"><rect width="70" height="10" rx="3"/><rect width="70" height="10" rx="3" transform="translate(0 15)"/><rect width="70" height="10" rx="3" transform="translate(0 30)"/><rect width="70" height="10" rx="3" transform="translate(0 45)"/></g></svg>
        </button>
      </li>
      <li>
        <button class="control--button" :class="{'is-selected': size === 'normal'}" @click="changeSize('normal')">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 55"><g fill-rule="nonzero"><rect width="70" height="15" rx="3"/><rect width="70" height="15" rx="3" transform="translate(0 20)"/><rect width="70" height="15" rx="3" transform="translate(0 40)"/></g></svg>
        </button>
      </li>
      <li v-if="type !== 'dashboard'">
        <button 
          class="control--button" 
          :class="{'is-active': sorting}" 
          @click="enableSorting(!sorting)"
        >
          <i class="fas" :class="sorting ? 'fa-check-circle':'fa-sort'"></i>  
        </button>
      </li>
    </ul>
  </nav>
</template>

<script>
import type from '../mixins/type'

export default {
  name: "listing-filter",
  mixins: [type],
  created() {
    const size = localStorage.getItem('listing-row-size');
    if(size !== null)
      this.$store.dispatch('general/setRowSize', size)
  },
  watch: {
    sorting(){
      if (this.sorting)
        this.$store.dispatch('selecting/selectAll', false)
    }
  },
  methods:{
    enableSorting(arg){
      this.$store.dispatch('general/setSorting', arg)
    },
    enableSelectAll(arg){
      this.$store.dispatch('selecting/selectAll', arg)
    },
    changeSize(size){
      this.$store.dispatch('general/setRowSize', size)
      localStorage.setItem('listing-row-size', size);
    },
  },
  computed: {
    size(){
      return this.$store.getters['general/getRowSize'];
    },
    sorting(){
      return this.$store.getters['general/getSorting'];
    },
    selectAll(){
      return this.$store.getters['selecting/selectAll'];
    }
  }
};
</script>