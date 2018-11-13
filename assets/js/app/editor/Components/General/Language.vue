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
      @input="switchLocale()"
    >
    <template slot="singleLabel" slot-scope="props">
      <span 
        class="flag mr-1"
        :class="props.option.flag|uppercase"
      ></span>
      <span>{{props.option.localisedname}}</span>
    </template>
    <template slot="option" slot-scope="props">
      <span 
        class="flag mr-1"
        :class="props.option.flag|uppercase"
      ></span>
      <span>{{props.option.localisedname}}</span>
    </template>
    </multiselect>
    
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'

export default {

  name: "editor-language",

  components: { Multiselect },

  props: ['label', 'locales'],


  mounted(){
    const url = new URLSearchParams(window.location.search);

    

    if(url.has('locale')){

      let current = this.locales.filter(locale =>
        locale.code === url.get('locale')
      )
      if(current.length > 0){
        this.locale = current[0];
      } else {
        this.locale = this.locales[0]
      }

    } else {

      this.locale = this.locales[0]

    }
  },

  data: () =>{
    return {
      locale: {}
    }
  },

  methods: {
    switchLocale(){
      const locale = this.locale.link;
      return window.location.href = locale;
    }
  }

};
</script>