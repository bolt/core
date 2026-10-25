<template>
    <button v-if="sorting" class="listing__row--move">
        <i class="fas px-2 fa-equals"></i>
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useGeneralStore } from '../../../store';

const generalStore = useGeneralStore();
const sorting = computed(() => generalStore.sorting);
</script>
