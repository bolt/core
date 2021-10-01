<template>
    <div class="listing__row--item is-actions edit-actions">
        <div class="btn-group">
            <a
                class="btn btn-secondary btn-block btn-sm text-nowrap"
                :href="record.extras.editLink"
                data-patience="virtue"
            >
                <i class="far fa-edit mr-1"></i> {{ labels.button_edit }}
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
                    <span class="status mr-1 is-published"></span>
                    {{ labels.status_to_publish }}
                </a>
                <a
                    v-if="record.status !== 'held'"
                    class="dropdown-item"
                    :href="record.extras.statusLink + '&status=held'"
                >
                    <span class="status mr-1 is-held"></span>
                    {{ labels.status_to_held }}
                </a>
                <a
                    v-if="record.status !== 'draft'"
                    class="dropdown-item"
                    :href="record.extras.statusLink + '&status=draft'"
                >
                    <span class="status mr-1 is-draft"></span>
                    {{ labels.status_to_draft }}
                </a>
                <a class="dropdown-item" :href="record.extras.duplicateLink">
                    <i class="far fa-w fa-copy"></i>
                    {{ labels.duplicate }} {{ record.extras.singular_name }}
                </a>
                <a
                    class="dropdown-item"
                    :href="record.extras.deleteLink"
                    data-confirmation="Are you sure you wish to delete this Content?"
                >
                    <i class="fas fa-w fa-trash"></i>
                    {{ labels.delete }} {{ record.extras.singular_name }}
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
                    <strong>{{ asdatetime(record.createdAt) }}</strong>
                </span>
                <span class="dropdown-item-text">
                    <i class="far fa-calendar-alt fa-w"></i>
                    {{ labels.published_on }}:
                    <strong>{{ asdatetime(record.publishedAt) }}</strong>
                </span>
                <span class="dropdown-item-text">
                    <i class="fas fa-redo fa-w"></i>
                    {{ labels.last_modified_on }}:
                    <strong>{{ asdatetime(record.modifiedAt) }}</strong>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import { DateTime } from 'luxon';
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
    methods: {
        trim(s,len) {
			if(!len) len = 50;
			if(s.length < len) return s;
		},
        asdatetime(string) {
            if (string) {
                return DateTime.fromISO(String(string)).toLocaleString(DateTime.DATETIME_MED);
            }
        }
    }
};
</script>
