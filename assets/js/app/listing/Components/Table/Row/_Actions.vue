<template>
  <div class="listing__row--item is-actions">
    <ul class="listing--actions">
      <li>
        <a :href="record.extras.editLink" class="link">
          <div class="btn-group">
            <button class="btn btn-grey btn-block btn-sm" type="button">
              <i class="far fa-edit mr-1"></i> Edit
            </button>
            <button
              type="button"
              class="btn btn-sm btn-grey dropdown-toggle dropdown-toggle-split"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right">...</div>
          </div>
        </a>
      </li>
      <li>
        <button
          type="button"
          class="listing--actions--quickedit"
          @click="quickEditor()"
        >
          <i class="far fa-caret-square-down mr-1"></i>Quick Edit
        </button>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'Actions',
  props: ['record'],
  methods: {
    quickEditor() {
      this.$emit('quickeditor', true);
    },
  },
};
</script>
