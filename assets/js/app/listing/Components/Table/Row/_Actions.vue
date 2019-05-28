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
      <div class="dropdown-menu dropdown-menu-right" style="width: 320px;">
        <a v-if="record.status === 'published'"  class="dropdown-item" :href="record.extras.link" target="_blank">
          <i class="fas fa-w fa-external-link-square-alt"></i>
          View on Site
        </a>
        <a v-if="record.status !== 'published'" class="dropdown-item" :href="record.extras.statusLink + '&status=published'">
          <span class="status mr-1 is-published"></span>
          Change status to 'publish'
        </a>
        <a v-if="record.status !== 'held'" class="dropdown-item" :href="record.extras.statusLink + '&status=held'">
          <span class="status mr-1 is-held"></span>
          Change status to 'held'
        </a>
        <a v-if="record.status !== 'draft'" class="dropdown-item" :href="record.extras.statusLink + '&status=draft'">
          <span class="status mr-1 is-draft"></span>
          Change status to 'draft'
        </a>
        <a class="dropdown-item" :href="record.extras.duplicateLink">
          <i class="far fa-w fa-copy"></i>
          Duplicate {{ record.extras.singular_name }}
        </a>
        <a class="dropdown-item" :href="record.extras.deleteLink">
          <i class="fas fa-w fa-trash"></i>
          Delete {{ record.extras.singular_name }}
        </a>

        <div class="dropdown-divider"></div>

        <span class="dropdown-item-text">
          <i class="fas fa-link fa-w"></i>
          Slug: <code :title="record.fieldValues.slug">{{ record.fieldValues.slug|trim(24) }}</code>
        </span>
        <span class="dropdown-item-text">
          <i class="fas fa-asterisk fa-w"></i>
          Created on: <strong>{{ record.createdAt|datetime }}</strong>
        </span>
        <span class="dropdown-item-text">
          <i class="far fa-calendar-alt fa-w"></i>
          Published on: <strong>{{ record.publishedAt|datetime }}</strong>
        </span>
        <span class="dropdown-item-text">
          <i class="fas fa-redo fa-w"></i>
          Last modified on: <strong>{{ record.modifiedAt|datetime }}</strong>
        </span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Actions',
  props: ['type', 'record'],
};
</script>
