<template>   
  <draggable :options="draggableOptions" v-model="records">
      <transition-group>
          <table-row 
            v-for="record in records" 
            :key="record.id"
            :record="record"
          ></table-row>
      </transition-group>
  </draggable>
</template>

<script>
import draggable from 'vuedraggable';
import Row from './Row';

export default {
  name: "listing-table",
  components: {
    "draggable": draggable,
    "table-row": Row,
  },
  data: () => {
    return {
      draggableOptions: {
        handle:'.listing__row--move', 
        forceFallback: true
      }
    };
  },
  computed: {
    records: {
      get() {
        return this.$store.getters['listing/getRecords'];
      },
      set(val) {
        this.$store.dispatch('listing/setRecords', val)
      }
    }
  }
};
</script>