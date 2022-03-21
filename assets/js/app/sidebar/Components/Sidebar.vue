<template>
    <nav class="admin__sidebar--nav">
        <sidebar-menu :menu="menu" :labels="labels"></sidebar-menu>
        <sidebar-toggler :version="version" :about-link="aboutLink" :labels="labels"></sidebar-toggler>
        <footer class="admin__sidebar--footer">
            <a :href="aboutLink">Bolt v<span class="sr-only">ersion </span>{{ version }}</a>
        </footer>
    </nav>
</template>

<script>
import Menu from './Menu';
import SidebarToggler from './_SidebarToggler';
import store from '../store';

export default {
    name: 'Sidebar',
    components: {
        'sidebar-menu': Menu,
        'sidebar-toggler': SidebarToggler,
    },
    props: {
        menu: Array,
        version: String,
        aboutLink: String,
        labels: Object,
    },
    created() {
        const slim = JSON.parse(localStorage.getItem('slim-sidebar'));

        if (slim) {
            store.dispatch('general/slimSidebar', slim);
        }
    },
};
</script>
