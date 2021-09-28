<template>
    <button v-if="sorting" class="listing__row--move">
        <i class="fas px-2 fa-equals"></i>
    </button>
</template>

<script>
import store from '../../../store';

export default {
    name: 'Sorting',
    computed: {
        sorting() {
            return store.getters['general/getSorting'];
        },
    },
};
</script>
