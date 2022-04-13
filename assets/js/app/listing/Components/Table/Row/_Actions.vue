<template>
    <div class="listing__row--item is-actions edit-actions">
        <div class="btn-group">
            <a
                class="btn btn-secondary btn-block btn-sm text-nowrap"
                :href="record.extras.editLink"
                data-patience="virtue"
            >
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
            <div class="edit-actions__dropdown dropdown-menu dropdown-menu-right" style="width: 320px;">
                <a
                    v-if="record.status === 'published'"
                    class="dropdown-item"
                    :href="record.extras.link"
                    target="_blank"
                >
                    <i class="fas fa-w fa-external-link-square-alt"></i>
                    {{ labels.view_on_site }}
                </a>
                <a
                    v-if="record.status !== 'published'"
                    class="dropdown-item"
                    :href="record.extras.statusLink + '&status=published'"
                >
                    <span class="status me-1 is-published"></span>
                    {{ labels.status_to_publish }}
                </a>
                <a
                    v-if="record.status !== 'held'"
                    class="dropdown-item"
                    :href="record.extras.statusLink + '&status=held'"
                >
                    <span class="status me-1 is-held"></span>
                    {{ labels.status_to_held }}
                </a>
                <a
                    v-if="record.status !== 'draft'"
                    class="dropdown-item"
                    :href="record.extras.statusLink + '&status=draft'"
                >
                    <span class="status me-1 is-draft"></span>
                    {{ labels.status_to_draft }}
                </a>
                <a class="dropdown-item" :href="record.extras.duplicateLink">
                    <i class="far fa-w fa-copy"></i>
                    {{ labels.duplicate }} {{ record.extras.singular_name }}
                </a>
                <a
                    class="dropdown-item"
                    :href="record.extras.deleteLink"
                    data-modal-title="Are you sure you wish to delete this Content?"
                    data-modal-button-deny="Cancel"
                    data-modal-button-accept="OK"
                    data-bs-toggle="modal"
                    data-bs-target="#resourcesModal"
                >
                    <i class="fas fa-w fa-trash"></i>
                    {{ labels.delete }} {{ record.extras.singular_name }}
                </a>

                <div class="dropdown-divider"></div>

                <span class="dropdown-item-text">
                    <i class="fas fa-link fa-w"></i>
                    {{ labels.slug }}:
                    <code :title="slug">{{ slug | trim(24) }}</code>
                </span>
                <span class="dropdown-item-text">
                    <i class="fas fa-asterisk fa-w"></i>
                    {{ labels.created_on }}:
                    <strong>{{ record.createdAt | datetime }}</strong>
                </span>
                <span class="dropdown-item-text">
                    <i class="far fa-calendar-alt fa-w"></i>
                    {{ labels.published_on }}:
                    <strong>{{ record.publishedAt | datetime }}</strong>
                </span>
                <span class="dropdown-item-text">
                    <i class="fas fa-redo fa-w"></i>
                    {{ labels.last_modified_on }}:
                    <strong>{{ record.modifiedAt | datetime }}</strong>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Actions',
    props: {
        type: String,
        record: Object,
        labels: Object,
    },
    computed: {
        slug() {
            if (this.record.fieldValues.slug === null) {
                return '';
            }
            if (typeof this.record.fieldValues.slug === 'string') {
                return this.record.fieldValues.slug;
            }
            // if slug has different locales, return the 0st one
            return this.record.fieldValues.slug[Object.keys(this.record.fieldValues.slug)[0]];
        },
    },
    created() {
        // console.log(this.labels);
    },
};
</script>
