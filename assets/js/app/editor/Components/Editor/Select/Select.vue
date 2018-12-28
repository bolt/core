<template>
  <div>
    <multiselect
      v-model="option"
      track-by="key"
      label="value"
      :options="options"
      :searchable="false"
      :show-labels="false"
      :multiple="multiple"
      :limit="1000"
      @input="serialiseValues"
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
      :value="serialised"
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
    let key = this.value;
    let value = '';

    console.log('options:', this.options);
    console.log('value:', this.value);
    console.log('key:', key);

    this.options.forEach(function(item) {
        if (item.selected == true) {
            value = item.value;
            console.log('selected: ', item.key);
        }
    });

    value = this.options;

    // This is still whack
    this.option.key = this.value;
    this.serialised = JSON.stringify(key);

  },

  data: () => {
    return {
      option: {
        key: null,
        selected: true,
        value: null
      },
      serialised: ''
    }
  },

  computed: {
    fieldName() {
      return this.name + '[]'
    },
  },

  methods: {
    serialiseValues(value) {
      var selected = [];

      if (value.key) {
        // Single
        selected.push(value.key);
      } else {
        // Multiple
        value.forEach(function(item) {
          selected.push(item.key);
        });
      }

      this.serialised = JSON.stringify(selected);
    }
  }
};
</script>