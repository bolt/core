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
        <a class="dropdown-item" :href="record.extras.link" target="_blank">View on Site</a>
        <a class="dropdown-item" :href="record.extras.statusLink + '?status=publish'">Change status to 'publish'</a>
        <a class="dropdown-item" :href="record.extras.statusLink + '?status=held'">Change status to 'held'</a>
        <a class="dropdown-item" :href="record.extras.statusLink + '?status=draft'">Change status to 'draft'</a>
        <a class="dropdown-item" :href="record.extras.duplicateLink">Duplicate [..]</a>
        <a class="dropdown-item" :href="record.extras.deleteLink">Delete [..]</a>
      </div>
    </div>

    <div class="btn-group">
      <button
              type="button"
              class="btn btn-sm btn-grey dropdown-toggle dropdown-toggle-split"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
      >
        <i class="fas mr-1" :class="record.extras.icon"></i>
        <template v-if="type === 'dashboard'">
          {{ record.contentType }}
        </template>
        <template v-else>
          Info
        </template>
        <span class="sr-only">Toggle Dropdown</span>
      </button>

      <div class="dropdown-menu dropdown-menu-right" style="width: 320px;">
        <span class="dropdown-item-text">
          <i class="fas fa-user fa-w"></i> Author: <strong>{{ record.authorName }}</strong>
        </span>
        <span class="dropdown-item-text">
          <i class="fas fa-w" :class="record.extras.icon"></i> ContentType: <strong><a :href="`/bolt/content/${record.contentType}`">{{ record.contentType }}</a>
          № {{ record.id }}</strong>
        </span>
        <span class="dropdown-item-text">
          <span class="status mr-1" :class="`is-${record.status}`"></span> Current status: <strong>{{ record.status }}</strong></span>
        <span class="dropdown-item-text">
          <i class="fas fa-link fa-w"></i> Slug: <code>{{ record.fieldValues.slug }}</code> </span>
        <span class="dropdown-item-text">
          <i class="fas fa-asterisk fa-w"></i> Created on: <strong>{{ record.createdAt|datetime }}</strong></span>
        <span class="dropdown-item-text">
          <i class="far fa-calendar-alt fa-w"></i> Published on: <strong>{{ record.publishedAt|datetime }}</strong></span>
        <span class="dropdown-item-text">
          <i class="fas fa-redo fa-w"></i> Last modified on: <strong>{{ record.modifiedAt|datetime }}</strong></span>
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
  props: ['type', 'record'],
  methods: {
    quickEditor() {
      this.$emit('quickeditor', true);
    },
  },
};
</script>
