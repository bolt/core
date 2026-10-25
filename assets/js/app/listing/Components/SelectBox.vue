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
                        <template #option="slotProps">
                            <span :class="slotProps.option.class"></span>
                            <span>
                                {{ slotProps.option.value }}
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

<script setup lang="ts">
import { ref, computed } from 'vue';
import Multiselect from 'vue-multiselect';
import { useSelectingStore } from '../store';
import type { BulkAction } from '../types';

const props = defineProps<{
    singular?: string;
    plural?: string;
    labels: Record<string, string>;
    csrftoken?: string;
    backendPrefix?: string;
}>();

const selectingStore = useSelectingStore();

const selectedAction = ref<BulkAction | null>(null);

const options = [
    {
        key: 'status/published',
        value: props.labels.status_to_published,
        selected: false,
        class: 'status me-1 is-published',
    },
    {
        key: 'status/draft',
        value: props.labels.status_to_draft,
        selected: false,
        class: 'status me-1 is-draft',
    },
    {
        key: 'status/held',
        value: props.labels.status_to_held,
        selected: false,
        class: 'status me-1 is-held',
    },
    {
        key: 'delete',
        value: props.labels.delete,
        selected: false,
        class: 'fas fa-w fa-trash',
    },
];

const selectedCount = computed(() => selectingStore.selectedCount);
const selected = computed(() => selectingStore.selected);

const postUrl = computed(() => {
    if (selectedAction.value) {
        return (props.backendPrefix || '') + 'bulk/' + selectedAction.value.key;
    }
    return '';
});

// To expose selectedAction so that the test can modify it
defineExpose({
    selectedAction,
});
</script>
