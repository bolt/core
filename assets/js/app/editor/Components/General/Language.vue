<template>
  <div class="form-group">
    <label>{{label}}</label>
    <multiselect
      v-model="locale"
      track-by="name"
      label="localisedname"
      :options="locales"
      :searchable="false"
      :show-labels="false"
      placeholder="select locale"
    >
    <template slot="singleLabel" slot-scope="props">
      <span 
        class="flag-icon mr-1"
        :class="`flag-icon-${props.option.flag}`"
      ></span>
      <span>{{props.option.localisedname}}</span>
    </template>
    <template slot="option" slot-scope="props">
      <span 
        class="flag-icon mr-1"
        :class="`flag-icon-${props.option.flag}`"
      ></span>
      <span>{{props.option.localisedname}}</span>
    </template>
    </multiselect>
    
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import  'flag-icon-css/css/flag-icon.min.css'

export default {
  name: "editor-language",
  props: ['label', 'current', 'locales'],
  components: { Multiselect },
  mounted(){
    let current = this.locales.filter(locale =>
      locale.code === this.current
    )
    this.locale = current[0];
  },
  data: () =>{
    return {
      locale: {}
    }
  }
};
</script>