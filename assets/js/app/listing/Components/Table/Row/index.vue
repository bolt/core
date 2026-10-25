<template>
    <transition-group tag="div" class="listing--container" :class="{ 'is-dashboard': type === 'dashboard' }">
        <!-- check box -->
        <row-checkbox
            v-if="type !== 'dashboard' && record.id !== undefined"
            :id="record.id"
            key="select"
        ></row-checkbox>

        <!-- row -->
        <div key="row" class="listing__row" :class="`is-${size}`">
            <!-- column details / excerpt -->
            <div class="listing__row--item is-details">
                <a class="listing__row--item-title text-decoration-none" :href="extras.editLink" :title="slug">
                    {{ raw(trim(extras.title ?? '', 62)) }}
                </a>
                <span v-if="extras.feature" class="badge" :class="`badge-${extras.feature}`">{{ extras.feature }}</span>
                <span class="listing__row--item-title-excerpt">{{ raw(extras.excerpt) }}</span>
            </div>
            <!-- end column -->

            <!-- column thumbnail -->
            <div v-if="size === 'normal' && extras.image" class="listing__row--item is-thumbnail">
                <img
                    :src="extras.image.thumbnail"
                    style="width: 108px"
                    loading="lazy"
                    :alt="extras.image.alt ?? undefined"
                />
            </div>
            <!-- end column -->

            <!-- column meta -->
            <row-meta :type="type ?? undefined" :size="size" :record="record"></row-meta>
            <!-- end column -->

            <!-- excerpt for small screens -->
            <div class="listing__row--item is-excerpt">
                <span>{{ extras.excerpt }}</span>
            </div>

            <!-- column actions -->
            <row-actions
                :type="type ?? undefined"
                :record="record"
                :size="size"
                :labels="labels.actions ?? {}"
            ></row-actions>
            <!-- end column -->
        </div>
    </transition-group>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import RowCheckbox from './_Checkbox.vue';
import RowMeta from './_Meta.vue';
import RowActions from './_Actions.vue';
import { trim, raw } from '../../../../../filters/string';
import { useGeneralStore } from '../../../store';
import type { ListingRecord } from '../../../types';

const props = defineProps<{
    record: ListingRecord;
    labels: { actions?: Record<string, string> };
}>();

const generalStore = useGeneralStore();
const type = computed(() => generalStore.type);
const size = computed(() => generalStore.rowSize);
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
