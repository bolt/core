<template>
  <button class="admin__sidebar--slim" @click="slimSidebar(!slim)">
    <i class="fas fa-exchange-alt"></i>
    <span class="sr-only">Toggle sidebar width</span>
  </button>
</template>

<script>
export default {
  name: 'SidebarFooter',
  props: ['version', 'aboutLink'],
  computed: {
    slim() {
      return this.$store.getters['general/slimSidebar'];
    },
  },
  watch: {
    slim() {
      const admin = document.querySelector('.admin');
      this.slim
        ? admin.classList.add('is-slim')
        : admin.classList.remove('is-slim');
      localStorage.setItem('slim-sidebar', this.slim);
    },
  },
  methods: {
    slimSidebar(arg) {
      this.$store.dispatch('general/slimSidebar', arg);
    },
  },
};
</script>
