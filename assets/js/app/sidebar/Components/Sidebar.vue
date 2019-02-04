<template>
  <nav class="admin__sidebar--nav">
    <div><sidebar-menu :menu="menu"></sidebar-menu></div>
    <sidebar-footer :version="version" :about-link="aboutLink"></sidebar-footer>
  </nav>
</template>

<script>
import Menu from './Menu';
import Footer from './_Footer';

export default {
  name: 'Sidebar',
  components: {
    'sidebar-menu': Menu,
    'sidebar-footer': Footer,
  },
  props: ['menu', 'version', 'aboutLink'],
  created() {
    const slim = JSON.parse(localStorage.getItem('slim-sidebar'));

    if (slim) {
      this.$store.dispatch('general/slimSidebar', slim);
    }
  },
};
</script>
