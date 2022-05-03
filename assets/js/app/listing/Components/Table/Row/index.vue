<template>
    <transition-group tag="div" class="listing--container" :class="{ 'is-dashboard': type === 'dashboard' }">
        <!-- check box -->
        <row-checkbox v-if="type !== 'dashboard'" :id="record.id" key="select"></row-checkbox>

        <!-- row -->
        <div key="row" class="listing__row" :class="`is-${size}`">
            <!-- column details / excerpt -->
            <div class="listing__row--item is-details">
                <a class="listing__row--item-title text-decoration-none" :href="record.extras.editLink" :title="slug">
                    {{ record.extras.title | trim(62) | raw }}
                </a>
                <span v-if="record.extras.feature" class="badge" :class="`badge-${record.extras.feature}`">{{
                    record.extras.feature
                }}</span>
                <span class="listing__row--item-title-excerpt">{{ record.extras.excerpt | raw }}</span>
            </div>
            <!-- end column -->

            <!-- column thumbnail -->
            <div v-if="size === 'normal' && record.extras.image" class="listing__row--item is-thumbnail">
                <img
                    :src="record.extras.image.thumbnail"
                    style="width: 108px;"
                    loading="lazy"
                    :alt="record.extras.image.alt"
                />
            </div>
            <!-- end column -->

            <!-- column meta -->
            <row-meta :type="type" :size="size" :record="record"></row-meta>
            <!-- end column -->

            <!-- excerpt for small screens -->
            <div class="listing__row--item is-excerpt">
                <span>{{ record.extras.excerpt }}</span>
            </div>

            <!-- column actions -->
            <row-actions :type="type" :record="record" :size="size" :labels="labels['actions']"></row-actions>
            <!-- end column -->
        </div>
    </transition-group>
</template>

<script>
import type from '../../../mixins/type';
import Checkbox from './_Checkbox';
import Meta from './_Meta';
import Actions from './_Actions';

export default {
    name: 'TableRow',
    components: {
        'row-checkbox': Checkbox,
        'row-meta': Meta,
        'row-actions': Actions,
    },
    mixins: [type],
    props: {
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
        size() {
            return this.$store.getters['general/getRowSize'];
        },
        sorting() {
            return this.$store.getters['general/getSorting'];
        },
    },
};
</script>
