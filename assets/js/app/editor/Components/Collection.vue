<template>
  <div>
    <div v-for="element in elements" :key="element.id">
      <div :is="element"></div>
    </div>

    <button class="btn btn-secondary" type="button" @click="addSet">
      {{ labels.add_collection_item }}
    </button>
  </div>
</template>

<script>
import Vue from 'vue';
var uniqid = require('locutus/php/misc/uniqid');

export default {
  name: 'EditorCollection',
  props: ['id', 'templates', 'existingFields', 'labels'],
  data() {
    let elements = [];
    this.existingFields.forEach(function(field) {
      elements.push(Vue.compile(field.html));
    });

    return {
      elements: elements,
    };
  },
  methods: {
    addSet() {
      let html = this.templates.author.html.replace(
        new RegExp(this.templates.author.hash, 'g'),
        uniqid(),
      );
      let res = Vue.compile(html);
      this.elements.push(res);
    },
  },
};
</script>
