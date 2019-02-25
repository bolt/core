<template>
  <div>
    <multiselect
      v-model="selected"
      track-by="key"
      label="value"
      :options="options"
      :show-labels="false"
      :limit="1000"
      :multiple="multiple"
      :taggable="taggable"
      :searchable="taggable"
      :allow-empty="allowempty"
      tag-placeholder="Add this as new tag"
      tag-position="bottom"
      @tag="addTag"
    >
      <template v-if="name === 'status'" slot="singleLabel" slot-scope="props">
        <span class="status mr-2" :class="`is-${props.option.key}`"></span>
        {{ props.option.key }}
      </template>
      <template v-if="name === 'status'" slot="option" slot-scope="props">
        <span class="status mr-2" :class="`is-${props.option.key}`"></span>
        {{ props.option.key }}
      </template>
    </multiselect>
    <input
      :id="id"
      type="hidden"
      :name="fieldName"
      :form="form"
      :value="sanitized"
    />
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect';

export default {
  name: 'EditorSelect',
  components: { Multiselect },
  props: [
    'value',
    'name',
    'id',
    'form',
    'options',
    'multiple',
    'taggable',
    'allowempty',
  ],
  data: () => {
    return {
      selected: [],
    };
  },
  computed: {
    sanitized() {
      let filtered;

      if (this.selected === null) {
        return JSON.stringify([]);
      } else if (this.selected.map) {
        filtered = this.selected.map(item => item.key);
        return JSON.stringify(filtered);
      } else {
        return JSON.stringify([this.selected.key]);
      }
    },
    fieldName() {
      return this.name + '[]';
    },
  },
  mounted() {
    const _values = this.value;
    const _options = this.options;

    let filterSelectedItems = _options.filter(item => {
      return _values.includes(item.key);
    });
    this.selected = filterSelectedItems;
  },
  methods: {
    addTag(newTag) {
      const tag = {
        key: newTag,
        value: newTag,
        selected: true,
      };
      this.options.push(tag);
      this.value.push(tag);
      this.selected.push(tag);
    },
  },
};
</script>
