<template>
  <div>
    <div v-for="element in elements" :key="element.id">
      <div :is="element"></div>
    </div>

    <editor-select
      ref="templateSelect"
      :value="initialSelectValue"
      :name="templateSelectName"
      :options="templateSelectOptions"
      :allowempty="false"
    ></editor-select>
    <button class="btn btn-secondary" type="button" @click="addCollectionItem">
      {{ labels.add_collection_item }}
    </button>
  </div>
</template>

<script>
import Vue from 'vue';
var uniqid = require('locutus/php/misc/uniqid');

export default {
  name: 'EditorCollection',
  props: ['templates', 'existingFields', 'labels'],
  data() {
    let elements = [];
    this.existingFields.forEach(function(field) {
      elements.push(Vue.compile(field.html));
    });

    let templateSelectOptions = [];

    this.templates.forEach(function(template) {
      templateSelectOptions.push({
        key: template.label,
        value: template.label,
      });
    });

    return {
      elements: elements,
      templateSelectName: 'templateSelect' + this.id,
      templateSelectOptions: templateSelectOptions,
    };
  },
  computed: {
    initialSelectValue() {
      return this.templateSelectOptions[0].key;
    },
  },
  methods: {
    addCollectionItem() {
      let template = this.getSelectedTemplate();

      let html = template.html.replace(
        new RegExp(template.hash, 'g'),
        uniqid(),
      );
      let res = Vue.compile(html);
      this.elements.push(res);
    },
    getSelectedTemplate() {
      let selectValue = this.$refs.templateSelect.selected;
      if (Array.isArray(selectValue)) {
        selectValue = selectValue[0];
      }

      return this.templates.find(
        template => template.label === selectValue.key,
      );
    },
  },
};
</script>
