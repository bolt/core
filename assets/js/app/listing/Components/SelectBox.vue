<template>
    <transition name="card">
        <div v-if="selectedCount > 0" class="card mb-3">
            <div class="card-header">
                <span class="is-primary me-1">{{ selectedCount }}</span>
                <template v-if="selectedCount === 1">{{ singular }}</template>
                <template v-else>{{ plural }}</template>
                {{ labels.selected }}
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
                        <template slot="option" slot-scope="props">
                            <span :class="props.option.class"></span>
                            <span>
                                {{ props.option.value }}
                            </span>
                        </template>
                    </multiselect>
                </div>

                <form :action="postUrl" method="post">
                    <input type="hidden" name="records" :value="selected" />
                    <input type="hidden" name="_csrf_token" :value="csrftoken" />
                    <div class="form-group">
                        <button
                            type="submit"
                            name="bulk-action"
                            class="btn btn-secondary"
                            :disabled="selectedAction === null"
                        >
                            {{ labels.update_all }}
                        </button>
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
        csrftoken: String,
        backendPrefix: String,
    },
    data() {
        return {
            selectedAction: null,
            options: [
                {
                    key: 'status/published',
                    value: this.labels.status_to_published,
                    selected: false,
                    class: 'status me-1 is-published',
                },
                {
                    key: 'status/draft',
                    value: this.labels.status_to_draft,
                    selected: false,
                    class: 'status me-1 is-draft',
                },
                {
                    key: 'status/held',
                    value: this.labels.status_to_held,
                    selected: false,
                    class: 'status me-1 is-held',
                },
                {
                    key: 'delete',
                    value: this.labels.delete,
                    selected: false,
                    class: 'fas fa-w fa-trash',
                },
            ],
        };
    },
    computed: {
        postUrl() {
            if (this.selectedAction) {
                return this.backendPrefix + 'bulk/' + this.selectedAction.key;
            }

            return '';
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
