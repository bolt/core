<template>
  <transition-group name="quickeditor" tag="div" class="listing--container">
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
        :style="`background-image: url(${thumbnail})`"
        v-if="size === 'normal'"
      ></div>
      <!-- end column -->

      <!-- column details -->
      <div 
        class="listing__row--item is-details" 
        v-html="excerpt"
      ></div>
      <!-- end column -->

      <!-- column meta -->
      <row-meta 
        :record-id="recordId" 
        :definition="definition" 
        :date="date" 
        :status="status" 
        :author="author" 
        :size="size"
      ></row-meta>
      <!-- end column -->
      
      <!-- column actions -->
      <row-actions 
        :record-id="recordId" 
        @quickeditor="quickEditor = $event"
      ></row-actions>
      <!-- end column -->
      
      <!-- column sorting -->
      <button 
        v-if="sorting" 
        class="listing__row--move"
      >
        <i class="fas px-2 fa-equals"></i>
      </button>
      <!-- end column -->

    </div>

    <!-- quick editor -->
    <row-quick-editor 
      v-if="quickEditor" 
      @quickeditor="quickEditor = $event" 
      key="test"
    ></row-quick-editor>

  </transition-group>
</template>

<script>
import QuickEditor from './Row/_QuickEditor';
import Actions from './Row/_Actions';
import Meta from './Row/_Meta';

export default {
  name: "listing-row",
  props: ["recordId", "definition", "excerpt", "date", "status", "thumbnail", "author"],
  components: {
    "row-quick-editor":   QuickEditor,
    "row-meta":           Meta,
    "row-actions":        Actions
  },
  created(){
    this.$root.$on('listing-row-size', data => this.size = data);
  },
  mounted() {
    this.$root.$on('listing-row-sorting', data => this.sorting = data);
  },
  data: () => {
    return {
      size: "normal",
      sorting: false,
      quickEditor: false,
    };
  },
};
</script>