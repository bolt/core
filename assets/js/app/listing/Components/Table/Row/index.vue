<template>
  <transition-group name="quickeditor" tag="div" class="listing--container" :class="{'is-dashboard': type === 'dashboard'}">

    <!-- check box -->
    <row-checkbox
      v-if="type !== 'dashboard'"
      :id="record.id"
      key="select"
    ></row-checkbox>

    <!-- row -->
    <div
      v-if="!quickEditor"
      class="listing__row"
      :class="`is-${size}`"
      key="row"
    >

      <!-- column thumbnail -->
      <div
        class="listing__row--item is-thumbnail"
        :style="`background-image: url(${record.image.path})`"
        v-if="size === 'normal'"
      ></div>
      <!-- end column -->

      <!-- column details -->
      <div
              class="listing__row--item is-details"
      >
        <a :href="record.editLink">{{ record.title }}</a>
        <span>{{ record.excerpt }}</span>
      </div>
      <!-- end column -->

      <!-- column meta -->
      <row-meta
        :type="type"
        :size="size"
        :meta="record"
      ></row-meta>
      <!-- end column -->

      <!-- column actions -->
      <row-actions
        :id="record.id"
        :size="size"
        @quickeditor="quickEditor = $event"
      ></row-actions>
      <!-- end column -->

      <!-- column sorting -->
      <row-sorting></row-sorting>
      <!-- end column -->

    </div>

    <!-- quick editor -->
    <row-quick-editor
      v-if="quickEditor"
      @quickeditor="quickEditor = $event"
      :size="size"
      key="quickeditor"
    ></row-quick-editor>

  </transition-group>
</template>

<script>
import type from '../../../mixins/type'
import Checkbox from './_Checkbox';
import Meta from './_Meta';
import Actions from './_Actions';
import Sorting from './_Sorting';
import QuickEditor from './_QuickEditor';

export default {
  name: "table-row",
  props: ["record"],
  mixins: [type],
  components: {
    "row-checkbox": Checkbox,
    "row-meta": Meta,
    "row-actions": Actions,
    "row-sorting": Sorting,
    "row-quick-editor": QuickEditor
  },
  data: () => {
    return {
      quickEditor: false,
    };
  },
  computed: {
    size(){
      return this.$store.getters['general/getRowSize']
    },
    sorting(){
      return this.$store.getters['general/getSorting']
    }
  }
};
</script>