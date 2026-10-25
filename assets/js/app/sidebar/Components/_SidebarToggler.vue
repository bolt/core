<template>
    <button class="admin__sidebar--slim focus-visible" @click="slimSidebar(!slim)">
        <i class="fas fa-exchange-alt"></i>
        <span class="sr-only">{{ labels.toggler }}</span>
    </button>
</template>

<script>
import { useGeneralStore } from '../store';

export default {
    name: 'SidebarFooter',
    props: {
        version: String,
        aboutLink: String,
        labels: Object,
    },
    computed: {
        slim() {
            const store = useGeneralStore(this.$pinia);
            return store.slimSidebar ? store.slimSidebar : false;
        },
    },
    watch: {
        slim() {
            const admin = document.querySelector('.admin');
            this.slim ? admin.classList.add('is-slim') : admin.classList.remove('is-slim');
            localStorage.setItem('slim-sidebar', this.slim);
        },
    },
    methods: {
        slimSidebar(arg) {
            useGeneralStore(this.$pinia).slimSidebar = arg;
        },
    },
};
</script>
