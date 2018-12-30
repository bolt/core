<template>
  <div>
    <multiselect
      v-model="selected"
      track-by="key"
      label="value"
      :options="options"
      :searchable="false"
      :show-labels="false"
      :limit="1000"
      :multiple="multiple"
    >

    <template slot="singleLabel" slot-scope="props" v-if="name === 'status'">
      <span class="status mr-2" :class="`is-${props.option.key}`"></span>{{props.option.key}}
    </template>
    <template slot="option" slot-scope="props" v-if="name === 'status'">
      <span class="status mr-2" :class="`is-${props.option.key}`"></span>{{props.option.key}}
    </template>

    </multiselect>
    
    <input
      type="text"
      :id="id"
      :name="fieldName" 
      :form="form"
      :value="sanitized"
    >
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'

export default {
  name: "editor-select",
  props: ['value', 'name', 'id', 'form', 'options', 'multiple'],
  components: { Multiselect },
  mounted(){
    const _values = this.value;
    const _options = this.options;

    let filterSelectedItems = _options.filter(item => {
      return _values.includes(item.key); 
    })

    this.selected = filterSelectedItems;
  },
  data: () => {
    return {
      selected: [],
    }
  },
  computed: {
    sanitized(){
      let filtered;
      filtered = this.selected.map(item => item.key);
      return JSON.stringify(filtered);
    },
    fieldName() {
      return this.name + '[]'
    },
  }
};
</script>