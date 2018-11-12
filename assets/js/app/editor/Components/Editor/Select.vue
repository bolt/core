<template>
  <div>
      <select class="selectize form-control" :id="id" :name="fieldName" :form="form">
        <option 
          v-for="(option, index) in selectOptions" 
          :key="index" 
          :value="option.key"
          :selected="option.key == value"
        >{{option.value}}</option>  
      </select>
  </div>
</template>

<script>
import selectize from 'selectize';

export default {
  name: "editor-select",
  props: ['value', 'label', 'name', 'id', 'form', 'options'],
  mounted(){
    $('.selectize').selectize({
      create: true,
      sortField: 'text'
    });
  },
  computed:{
    selectOptions(){
      return JSON.parse(this.options);
    },
    fieldName(){
      return this.name + '[]'
    }
  }
};
</script>