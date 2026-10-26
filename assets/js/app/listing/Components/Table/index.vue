<template>
    <div class="listing__records">
        <draggable v-model="records" handle=".listing__row--move" force-fallback="true">
            <transition-group>
                <table-row v-for="record in records" :key="record.id" :record="record" :labels="labels"></table-row>
            </transition-group>
        </draggable>
    </div>
</template>

<script>
import { mapWritableState } from 'pinia';
import draggable from 'vuedraggable';
import Row from './Row';
import { useListingStore } from '../../store';

export default {
    name: 'ListingTable',
    components: {
        draggable: draggable,
        'table-row': Row,
    },
    props: {
        labels: Object,
    },
    computed: {
        ...mapWritableState(useListingStore, ['records']),
    },
};
</script>
