<template>
    <div class="listing__row--item is-actions edit-actions">
        <div class="btn-group">
            <a class="btn btn-secondary btn-block btn-sm text-nowrap" :href="extras.editLink" data-patience="virtue">
                <i class="far fa-edit me-1"></i> {{ labels.button_edit }}
            </a>
            <button
                type="button"
                class="btn btn-sm btn-secondary edit-actions__dropdown-toggler dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
            >
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <div class="edit-actions__dropdown dropdown-menu dropdown-menu-right" style="width: 320px">
                <a v-if="record.status === 'published'" class="dropdown-item" :href="extras.link" target="_blank">
                    <i class="fas fa-w fa-external-link-square-alt"></i>
                    {{ labels.view_on_site }}
                </a>
                <a
                    v-if="extras.statusLink && record.status !== 'published'"
                    class="dropdown-item"
                    :href="extras.statusLink + '&status=published'"
                >
                    <span class="status me-1 is-published"></span>
                    {{ labels.status_to_publish }}
                </a>
                <a
                    v-if="extras.statusLink && record.status !== 'held'"
                    class="dropdown-item"
                    :href="extras.statusLink + '&status=held'"
                >
                    <span class="status me-1 is-held"></span>
                    {{ labels.status_to_held }}
                </a>
                <a
                    v-if="extras.statusLink && record.status !== 'draft'"
                    class="dropdown-item"
                    :href="extras.statusLink + '&status=draft'"
                >
                    <span class="status me-1 is-draft"></span>
                    {{ labels.status_to_draft }}
                </a>
                <a class="dropdown-item" :href="extras.duplicateLink">
                    <i class="far fa-w fa-copy"></i>
                    {{ labels.duplicate }} {{ extras.singular_name }}
                </a>
                <a
                    class="dropdown-item"
                    :href="extras.deleteLink"
                    data-modal-title="Are you sure you wish to delete this Content?"
                    data-modal-button-deny="Cancel"
                    data-modal-button-accept="OK"
                    data-bs-toggle="modal"
                    data-bs-target="#resourcesModal"
                >
                    <i class="fas fa-w fa-trash"></i>
                    {{ labels.delete }} {{ extras.singular_name }}
                </a>

                <div class="dropdown-divider"></div>

                <span class="dropdown-item-text">
                    <i class="fas fa-link fa-w"></i>
                    {{ labels.slug }}:
                    <code :title="slug">{{ trim(slug, 24) }}</code>
                </span>
                <span class="dropdown-item-text">
                    <i class="fas fa-asterisk fa-w"></i>
                    {{ labels.created_on }}:
                    <strong>{{ datetime(record.createdAt) }}</strong>
                </span>
                <span class="dropdown-item-text">
                    <i class="far fa-calendar-alt fa-w"></i>
                    {{ labels.published_on }}:
                    <strong>{{ datetime(record.publishedAt) }}</strong>
                </span>
                <span class="dropdown-item-text">
                    <i class="fas fa-redo fa-w"></i>
                    {{ labels.last_modified_on }}:
                    <strong>{{ datetime(record.modifiedAt) }}</strong>
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { trim } from '../../../../../filters/string';
import { datetime } from '../../../../../filters/date';
import type { ListingRecord } from '../../../types';

const props = defineProps<{
    type?: string;
    // Row/index.vue forwards `:size` to every row column; _Actions doesn't vary by
    // size, but declaring it avoids attribute fallthrough onto the root element.
    size?: string;
    record: ListingRecord;
    labels: Record<string, string>;
}>();

const extras = computed(() => props.record.extras ?? {});

const slug = computed(() => {
    const slugValue = props.record.fieldValues?.slug;
    if (slugValue === null || slugValue === undefined) {
        return '';
    }
    if (typeof slugValue === 'string') {
        return slugValue;
    }
    // if slug has different locales, return the 0st one
    const firstLocale = Object.keys(slugValue)[0];
    return firstLocale ? slugValue[firstLocale] : '';
});
</script>
