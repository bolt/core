<template>
  <div class="listing__row--item is-actions">
    <div class="btn-group">
      <a :href="record.extras.editLink" class="btn btn-secondary btn-block btn-sm">
        <i class="far fa-edit mr-1"></i> Edit
      </a>
      <button
        type="button"
        class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split"
        data-toggle="dropdown"
        aria-haspopup="true"
        aria-expanded="false"
      >
        <span class="sr-only">Toggle Dropdown</span>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="#">View on Site</a>
        <a class="dropdown-item" href="#">Change status to 'publish'</a>
        <a class="dropdown-item" href="#">Change status to 'held'</a>
        <a class="dropdown-item" href="#">Change status to 'draft'</a>
        <a class="dropdown-item" href="#">Duplicate [..]</a>
        <a class="dropdown-item" href="#">Delete [..]</a>
      </div>
    </div>

    <div class="btn-group">
      <button
              type="button"
              class="btn btn-sm btn-grey dropdown-toggle dropdown-toggle-split"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
      ><i class="fas fa-info-circle"></i> Info
        <span class="sr-only">Toggle Dropdown</span>
      </button>
      <div class="dropdown-menu dropdown-menu-right">
        <span class="dropdown-item-text">Author: </span>
        <span class="dropdown-item-text">Current status: </span>
        <span class="dropdown-item-text">Slug: </span>
        <span class="dropdown-item-text">Created on: </span>
        <span class="dropdown-item-text">Published on: </span>
        <span class="dropdown-item-text">Last edited on: </span>
      </div>
    </div>
        <!-- <button
          type="button"
          class="listing--actions--quickedit"
          @click="quickEditor()"
        >
          <i class="far fa-caret-square-down mr-1"></i>Quick Edit
        </button> -->
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
