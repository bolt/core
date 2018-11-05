<template>
  <div class="listing--checkbox" key="checkbox">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" :id="`checkbox_${recordId}`" v-model="selected">
        <label class="custom-control-label" :for="`checkbox_${recordId}`" @click="checkBox()"></label>
    </div>
  </div>
</template>

<script>
  export default {
    name: "checkbox",
    props: ["recordId"],
    mounted() {
      this.$root.$on('listing-selectall', data => this.selected = data);
    },
    data: () => {
      return {
        selected: false,
      };
    },
    watch: {
      selected(){
        if(this.selected){

        } else {

        }
      }
    },
    methods: {
      checkBox(){
        let selected = this.selected;
        if(selected){
          selected = true
          this.$root.$emit('listing-row-selected', this.recordId);
        } else {
          this.$root.$emit('listing-row-unselected', this.recordId);
          selected = false
        }
      },
    }
  };
</script>