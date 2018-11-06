<template>
  <transition name="card">
    <div class="card mb-3" v-if="recordCount > 0">
      <div class="card-header">
        <span class="badge is-primary mr-1">{{recordCount}}</span>
        <template v-if="recordCount === 1">{{ctSingular}}</template>
        <template v-else>{{ct}}</template>
        Selected
      </div>
      <div class="card-body">
        <h4>selected record ids passed</h4>
      <p><em>(these can be used with something like axios to bulk modify/delete)</em></p> 
<pre>
<code>{{records}}</code>
</pre> 
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "listing-selected-box",
  props: ["ct", "ctSingular"],
  mounted() {
    this.$root.$on('listing-row-selected', id => {
      this.recordCount++
      this.records.push(id);
    });
    this.$root.$on('listing-row-unselected', id => {
      this.recordCount--
      let index = this.records.indexOf(id);
      if (index > -1) {
        this.records.splice(index, 1);
      }
    });
  },
  data: () => {
    return {
      recordCount: 0,
      records: []
    };
  },
  methods:{
    
  }
};
</script>