<template>
  <transition name="card">
    <div v-if="selectedCount > 0" class="card mb-3">
      <div class="card-header">
        <span class="is-primary mr-1">{{ selectedCount }}</span>
        <template v-if="selectedCount === 1">{{ singular }}</template>
        <template v-else>{{ plural }}</template>
        {{ labels.card_header.selected }}
      </div>
      <div class="card-body">
        <div class="form-group">
          <multiselect
            v-model="selectedAction"
            :allow-empty="false"
            :multiple="false"
            :show-labels="false"
            label="value"
            track-by="key"
            :options="options"
          >
          </multiselect>
        </div>

        <form
          v-if="selectedAction !== null"
          :action="postUrl"
          method="post"
        >
          <input type="hidden" name="records" :value="selected">
          <div class="form-group">
            <button type="submit" class="btn btn-secondary">{{ selectedAction.value }} all</button>
          </div>
        </form>
      </div>
    </div>
  </transition>
</template>

<script>
import Multiselect from 'vue-multiselect';


export default {
  name: 'ListingSelectedBox',
  components: { Multiselect },
  props: {
    singular: String,
    plural: String,
    labels: Object,
  },
  data: () => {
    return {
      selectedAction: null,
      options: [
        { key: "delete", value: "Delete", selected: false},
        { key: "status/published", value: "Publish", selected: false},
      ]
    }
  },
  computed: {
    postUrl() {
      if (this.selectedAction) {
        return "/bolt/bulk/" + this.selectedAction.key;
      }

      return "";
    },
    selectedCount() {
      return this.$store.getters['selecting/selectedCount'];
    },
    selected() {
      return this.$store.getters['selecting/selected'];
    },
    order() {
      return this.$store.getters['listing/getOrder'];
    },
  },
};
</script>
