<template>
  <transition name="card">
    <div v-if="selectedCount > 0" class="card mb-3">
      <div class="card-header">
        <span class="badge is-primary mr-1">{{ selectedCount }}</span>
        <template v-if="selectedCount === 1">{{ singular }}</template>
        <template v-else>{{ plural }}</template>
        Selected
      </div>
      <div class="card-body">
        <h4>selected record ids passed</h4>
        <p>
          <em
            >(these can be used with something like axios to bulk
            modify/delete)</em
          >
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
  props: ['singular', 'plural'],
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
