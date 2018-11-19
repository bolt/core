<template>
  <div>
    <button class="admin__sidebar--slim" @click="slimSidebar(!slim)"><i class="fas fa-arrows-alt-h"></i></button>
    <footer class="admin__sidebar--footer">
      <a :href="aboutLink">Bolt {{version}}</a>
    </footer>
  </div>
</template>

<script>
export default {
  name: "sidebar-footer",
  props: ["version", "aboutLink"],
  methods: {
    slimSidebar(arg){
      this.$store.dispatch('general/slimSidebar', arg);
    }
  },
  watch: {
    slim(){
      const admin = document.querySelector('.admin');
      this.slim ? admin.classList.add('is-slim'):admin.classList.remove('is-slim');
      localStorage.setItem('slim-sidebar', this.slim);
    }
  },
  computed: {
    slim(){
      return this.$store.getters['general/slimSidebar']
    }
  }
};
</script>