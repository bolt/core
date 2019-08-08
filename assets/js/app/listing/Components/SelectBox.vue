<template>
  <transition name="card">
    <div v-if="selectedCount > 0" class="card mb-3">
      <div class="card-header">
        <span class="badge is-primary mr-1">{{ selectedCount }}</span>
        <template v-if="selectedCount === 1">{{ singular }}</template>
        <template v-else>{{ plural }}</template>
        {{ labels.card_header.selected }}
      </div>
      <div class="card-body">
        <h4>{{ labels.card_body.records_passed }}</h4>
        <p>
          <em>{{ labels.card_body.remark }}</em>
        </p>
        <pre>
<code>{{selected}}</code>
</pre>

        <pre>
<code>{{order}}</code>
</pre>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'ListingSelectedBox',
  props: ['singular', 'plural', 'labels'],
  computed: {
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
