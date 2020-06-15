<template>
  <div :id="`multiselect-${id}`" :class="classname">
    <multiselect
        v-model="selected"
        :allow-empty="allowempty"
        :limit="1000"
        :multiple="multiple"
        :options="options"
        :searchable="taggable"
        :show-labels="false"
        :taggable="taggable"
        :disabled="readonly"
        :data-errormessage="errormessage"
        label="value"
        tag-placeholder="Add this as new tag"
        tag-position="bottom"
        track-by="key"
        ref="vselect"
        @tag="addTag"
      >
        <template v-if="name === 'status'" slot="singleLabel" slot-scope="props">
          <span class="status mr-2" :class="`is-${props.option.key}`"></span>
          {{ props.option.value }}
        </template>
        <template v-if="name === 'status'" slot="option" slot-scope="props">
          <span class="status mr-2" :class="`is-${props.option.key}`"></span>
          {{ props.option.value }}
        </template>
          <template v-if="name !== 'status'" slot="tag" slot-scope="props">
            <span v-on:drop="drop($event)" v-on:dragover="allowDrop($event)">
              <span :id=props.option.key class="multiselect__tag" :key=props.option.value draggable="true" v-on:dragstart="drag($event)">
                <span v-text="props.option.value"></span>
                <i class="multiselect__tag__drag fas fa-grip-lines"></i>
                <i tabindex="1" @keypress.enter.prevent="removeElement(props.option)"  @mousedown.prevent="removeElement(props.option)" class="multiselect__tag-icon"></i>
              </span>
            </span>
          </template>
      </multiselect>
    <input
      :id="id"
      :name="name"
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
  props: {
    value: Array | String,
    name: String,
    id: String,
    form: String,
    options: Array,
    multiple: Boolean,
    allowempty: Boolean,
    taggable: Boolean,
    readonly: Boolean,
    classname: String,
    errormessage: String | Boolean, //string if errormessage is set, and false otherwise
  },
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

    let filterSelectedItems = _values.map(value => {
      const item = _options.filter(opt => opt.key === value);
      if (item) {
        return item[0];
      }
    });

    if (filterSelectedItems.length === 0) {
      filterSelectedItems = [_options[0]];
    }

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
    removeElement: function(element) {
      this.$refs.vselect.removeElement(element);
    },
    drop(e) {
      e.preventDefault();

      const incomingId = e.dataTransfer.getData("text");

      /**
       * JS Draggable API allows elements to be dropped inside child nodes
       * We have to find the parent with draggable='true' to get the id.
       */
      const outgoingId = this.findDropElement(e.target).id;

      const outgoingIndex = this.value.indexOf(outgoingId);

      var newValue = this.value;
      newValue.splice(newValue.indexOf(incomingId), 1);
      newValue.splice(outgoingIndex, 0, incomingId);
      this.value = newValue;

      console.log(newValue);


      const incomingElement = this.selected.find(el => el.key === incomingId);
      const outgoingElement = this.selected.find(el => el.key === outgoingId);
      var newSelected = this.selected;

      newSelected.splice(newSelected.indexOf(incomingElement), 1);
      newSelected.splice(newSelected.indexOf(outgoingElement), 0, incomingElement);

      console.log(newSelected);

      this.selected = newSelected;
    },
    findDropElement(el) {
      while (! el.hasAttribute('draggable')) {
        el = el.parentNode;
      }

      return el;
    },
    allowDrop(e) {
      e.preventDefault();
    },
    drag(e) {
      e.dataTransfer.setData("text", e.target.id);
    }
  },
};
</script>
