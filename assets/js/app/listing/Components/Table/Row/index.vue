<template>
  <transition-group
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
      key="row"
      class="listing__row"
      :class="`is-${size}`"
      @mouseleave="leave"
    >
      <!-- column details -->
      <div class="listing__row--item is-details">
        <a class="listing__row--item-title" :href="record.extras.editLink" :title="record.fieldValues.slug">{{ record.extras.title | trim(62) }}</a>
        <span class="listing__row--item-title-excerpt">{{ record.extras.excerpt }}</span>
      </div>
      <!-- end column -->

      <!-- column meta -->
      <row-meta :type="type" :size="size" :record="record"></row-meta>
      <!-- end column -->

      <!-- column thumbnail -->
      <div
        v-if="size === 'normal' && record.extras.image !== null"
        class="listing__row--item is-thumbnail"
        :style="`background-image: url(${record.extras.image.path})`"
      ></div>
      <!-- end column -->

      <div class="listing__row--item is-excerpt">
        <span>{{ record.extras.excerpt }}</span>
      </div>

      <!-- column actions -->
      <row-actions
        :type="type"
        :record="record"
        :size="size"
      ></row-actions>
      <!-- end column -->

      <!-- column sorting -->
      <row-sorting></row-sorting>
      <!-- end column -->
    </div>

  </transition-group>
</template>

<script>
import type from '../../../mixins/type';
import Checkbox from './_Checkbox';
import Meta from './_Meta';
import Actions from './_Actions';
import Sorting from './_Sorting';

export default {
  name: 'TableRow',
  components: {
    'row-checkbox': Checkbox,
    'row-meta': Meta,
    'row-actions': Actions,
    'row-sorting': Sorting,
  },
  mixins: [type],
  props: ['record'],
  methods: {
    leave(event) {
      // When we 'leave' the row, make sure we close the dropdown.
      $('.dropdown-toggle[aria-expanded="true"').dropdown('toggle');
    },
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
