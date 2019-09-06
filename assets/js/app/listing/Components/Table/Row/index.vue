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
      <!-- column thumbnail -->
      <div
              class="listing__row--item is-thumbnail"
        v-if="size === 'normal' && record.extras.image !== null"
        class="listing__row--item is-thumbnail"
        :style="`background-image: url('${record.extras.image.thumbnail}')`"
      ></div>
      <!-- end column -->

      <!-- column details -->
      <div class="listing__row--item is-details">
        <a
          class="listing__row--item-title"
          :href="record.extras.editLink"
          :title="record.fieldValues.slug"
        >
          {{ record.extras.title | trim(62) }}
        </a>
        <span class="listing__row--item-title-excerpt">{{
          record.extras.excerpt
        }}</span>
      </div>
      <!-- end column -->

      <!-- column meta -->
      <row-meta :type="type" :size="size" :record="record"></row-meta>
      <!-- end column -->

      <!-- excerpt for small screens -->
      <div class="listing__row--item is-excerpt">
        <span>{{ record.extras.excerpt }}</span>
      </div>

      <!-- column actions -->
      <row-actions
        :type="type"
        :record="record"
        :size="size"
        :labels="labels['actions']"
      ></row-actions>
      <!-- end column -->
    </div>
  </transition-group>
</template>

<script>
import type from '../../../mixins/type';
import Checkbox from './_Checkbox';
import Meta from './_Meta';
import Actions from './_Actions';
import $ from 'jquery';

export default {
  name: 'TableRow',
  components: {
    'row-checkbox': Checkbox,
    'row-meta': Meta,
    'row-actions': Actions,
  },
  mixins: [type],
  props: ['record', 'labels'],
  computed: {
    size() {
      return this.$store.getters['general/getRowSize'];
    },
    sorting() {
      return this.$store.getters['general/getSorting'];
    },
  },
  methods: {
    leave() {
      // When we 'leave' the row, make sure we close the dropdown.
      $('.dropdown-toggle[aria-expanded="true"]').dropdown('toggle');
    },
  },
};
</script>
