<template>
    <button class="admin__sidebar--slim focus-visible" @click="toggleSlimSidebar(!slim)">
        <i class="fas fa-exchange-alt"></i>
        <span class="sr-only">{{ labels.toggler }}</span>
    </button>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue';
import { useGeneralStore } from '../store';

defineProps<{
    version?: string;
    aboutLink?: string;
    labels: Record<string, string>;
}>();

const generalStore = useGeneralStore();
const slim = computed(() => generalStore.slimSidebar || false);

watch(
    slim,
    newSlim => {
        const admin = document.querySelector('.admin');
        if (admin) {
            if (newSlim) {
                admin.classList.add('is-slim');
            } else {
                admin.classList.remove('is-slim');
            }
        }
        localStorage.setItem('slim-sidebar', String(newSlim));
    },
    { immediate: true },
);

function toggleSlimSidebar(arg: boolean) {
    generalStore.slimSidebar = arg;
}
</script>
