<template>
    <button v-if="sorting" class="listing__row--move">
        <i class="fas px-2 fa-equals"></i>
    </button>
</template>

<script>
import { mapState } from 'pinia';
import { useGeneralStore } from '../../../store';

export default {
    name: 'Sorting',
    computed: {
        ...mapState(useGeneralStore, ['sorting']),
    },
};
</script>
