<template>
  <div
    class="admin__toolbar--body"
    :class="contrast ? 'is-light' : 'is-dark'"
    role="toolbar"
  >
    <div class="toolbar-item toolbar-item__brand">

      <img src="/assets/logos/bolt_logo_dashboard.svg" alt="⚙️ Bolt" height="28" />

      <ul class="toolbar-menu">
        <li><a href="https://bolt.cm/" target="_blank">Visit Bolt.cm</a></li>
        <li>
          <a href="https://docs.bolt.cm/" target="_blank">{{
            labels['about.bolt_documentation']
          }}</a>
        </li>
      </ul>
    </div>
    <div class="toolbar-item toolbar-item__site">
      <a href="/" target="_blank">
        <i class="fas fa-globe-americas mr-2"></i>{{ labels['action.view_site'] }}
      </a>
    </div>
    <div class="toolbar-item toolbar-item__new">
      <i class="fas fa-magic mr-2"></i>{{ labels['action.create_new'] }} …
      <ul class="toolbar-menu">
        <li v-for="(item, index) in createMenu" :key="index">
          <a :href="item.link_new">{{ item.singular_name }}</a>
        </li>
      </ul>
    </div>
    <div class="toolbar-item toolbar-item__profile">
      <i class="fas fa-user mr-2"></i>{{ labels['general.greeting'] }}
      <ul class="toolbar-menu">
        <li>
          <a :href="backend_prefix + 'profile-edit'">{{ labels['action.edit_profile'] }}</a>
        </li>
        <li>
          <a :href="backend_prefix + 'logout'">{{ labels['action.logout'] }}</a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
const tinycolor = require('tinycolor2');

export default {
  name: 'Toolbar',
  props: ['siteName', 'menu', 'labels', 'backend_prefix'],
  computed: {
    contrast() {
      const color = tinycolor(this.toolbarColor);
      return color.isLight();
    },
    createMenu() {
      return this.menu.filter(item => {
        return (
          (!item.singleton && item.singular_name) ||
          (item.singleton && (item.submenu === null || item.submenu.length < 1))
        );
      });
    },
    toolbarColor() {
      return this.$store.getters['general/toolbarColor'];
    },
  },
  created() {
    const color = getComputedStyle(document.body).getPropertyValue(
      '--admin-toolbar',
    );
    this.$store.dispatch('general/toolbarColor', color);
  },
};
</script>
