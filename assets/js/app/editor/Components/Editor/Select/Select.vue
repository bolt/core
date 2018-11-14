<template>
  <div>
    <multiselect
      v-model="option"
      track-by="key"
      label="value"
      :options="options"
      :searchable="false"
      :show-labels="false"
      :limit="1"
    >
    <template slot="singleLabel" slot-scope="props" v-if="name === 'status'">
      <span class="status mr-2" :class="`is-${props.option.key}`"></span>{{props.option.key}}
    </template>
    <template slot="option" slot-scope="props" v-if="name === 'status'">
      <span class="status mr-2" :class="`is-${props.option.key}`"></span>{{props.option.key}}
    </template>
    </multiselect>
    <input 
      type="hidden"
      :id="id"
      :name="fieldName" 
      :form="form"
      :value="option.key"
    >
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'

export default {
  name: "editor-select",
  props: ['value', 'name', 'id', 'form', 'options'],
  components: { Multiselect },

  mounted(){
    let key = this.value;
    let value = '';
    this.options.forEach(function(item) {
        if (item.key == key) {
            value = item.value;
        }
    });

    this.option.key = key;
    this.option.value = value;
  },

  data: () => {
    return {
      option: {
        key: null,
        selected: true,
        value: null
      }
    }
  },

  computed:{
    fieldName(){
      return this.name + '[]'
    }
  }
};
</script>