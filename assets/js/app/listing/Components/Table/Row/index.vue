<template>
  <transition-group
    name="quickeditor"
    tag="div"
    class="listing--container"
    :class="{ 'is-dashboard': type === 'dashboard' }"
  >
    <!-- check box -->
    <row-checkbox
      v-if="type !== 'dashboard'"
      :id="record.id"
      key="select"
    ></row-checkbox>

    <!-- row -->
    <div
      v-if="!quickEditor"
      key="row"
      class="listing__row"
      :class="`is-${size}`"
    >
      <!-- column thumbnail -->
      <div
        v-if="size === 'normal' && record.extras.image !== null"
        class="listing__row--item is-thumbnail"
        :style="`background-image: url(${record.extras.image.path})`"
      ></div>
      <!-- end column -->

      <!-- column details -->
      <div class="listing__row--item is-details">
        <a :href="record.extras.editLink">{{ record.extras.title }}</a>
        <span>{{ record.extras.excerpt }}</span>
      </div>
      <!-- end column -->

      <!-- column meta -->
      <row-meta :type="type" :size="size" :record="record"></row-meta>
      <!-- end column -->

      <!-- column actions -->
      <row-actions
        :record="record"
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
      key="quickeditor"
      :size="size"
      @quickeditor="quickEditor = $event"
    ></row-quick-editor>
  </transition-group>
</template>

<script>
import type from '../../../mixins/type';
import Checkbox from './_Checkbox';
import Meta from './_Meta';
import Actions from './_Actions';
import Sorting from './_Sorting';
import QuickEditor from './_QuickEditor';

export default {
  name: 'TableRow',
  components: {
    'row-checkbox': Checkbox,
    'row-meta': Meta,
    'row-actions': Actions,
    'row-sorting': Sorting,
    'row-quick-editor': QuickEditor,
  },
  mixins: [type],
  props: ['record'],
  data: () => {
    return {
      quickEditor: false,
    };
  },
  computed: {
    size() {
      return this.$store.getters['general/getRowSize'];
    },
    sorting() {
      return this.$store.getters['general/getSorting'];
    },
  },
};
</script>
