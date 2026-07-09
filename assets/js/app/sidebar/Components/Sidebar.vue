<template>
    <nav class="admin__sidebar--nav">
        <sidebar-menu :menu="menu" :labels="labels"></sidebar-menu>
        <sidebar-toggler :version="version" :about-link="aboutLink" :labels="labels"></sidebar-toggler>
        <footer class="admin__sidebar--footer">
            <a :href="aboutLink">Bolt v<span class="sr-only">ersion </span>{{ version }}</a>
        </footer>
    </nav>
</template>

<script setup lang="ts">
import SidebarMenu from './Menu/index.vue';
import SidebarToggler from './_SidebarToggler.vue';
import { useGeneralStore } from '../store';
import type { SidebarMenuItem } from '../types';

defineProps<{
    menu: SidebarMenuItem[];
    version: string;
    aboutLink: string;
    labels: Record<string, string>;
}>();

const slim = JSON.parse(localStorage.getItem('slim-sidebar') || 'false');

if (slim) {
    const generalStore = useGeneralStore();
    generalStore.slimSidebar = slim;
}
</script>
