<template>
  <div
    class="admin__toolbar--body"
    :class="contrast ? 'is-light' : 'is-dark'"
    role="toolbar"
  >
    <div class="toolbar-item btn-group toolbar-item__brand">
        <img
          src="/assets/images/bolt_logo_dashboard.svg"
          alt="⚙️ Bolt"
          height="28"
        />
    </div>

    <form
      :action="backendPrefix"
      class="toolbar-item toolbar-item__filter input-group"
    >
      <input
        id="global-search"
        type="text"
        class="form-control"
        :placeholder="labels['listing.placeholder_search']"
        name="filter"
        :value="filterValue"
      />
      <div class="input-group-append">
        <button class="btn btn-tertiary" type="submit">
          <i class="fas fa-search"></i>{{ labels['listing.button_search'] }}
        </button>
      </div>
    </form>

    <div class="toolbar-item toolbar-item__site">
      <a href="/" target="_blank">
        <i class="fas fa-sign-out-alt"></i>{{ labels['action.view_site'] }}
      </a>
    </div>

    <div class="toolbar-item btn-group toolbar-item__new">
      <button
        class="btn new-item__dropdown-toggler dropdown-toggle"
        type="button"
        data-toggle="dropdown"
        data-display="static"
        aria-haspopup="true"
        aria-expanded="false"
      >
        <i class="fas fa-magic"></i>{{ labels['action.create_new'] }}…
      </button>
      <div class="new-item__dropdown dropdown-menu">
        <ul>
          <li v-for="(item, index) in createMenu" :key="index">
            <a :href="item.link_new"
              ><i :class="`fa fa-fw ${item.icon}`"></i>
              {{ item.singular_name }}</a
            >
          </li>
        </ul>
      </div>
    </div>

    <div class="toolbar-item btn-group toolbar-item__profile">
      <button
        class="btn user profile__dropdown-toggler dropdown-toggle"
        type="button"
        data-toggle="dropdown"
        data-display="static"
        aria-haspopup="true"
        aria-expanded="false"
      >
        <i class="fas fa-user"></i>{{ labels['general.greeting'] }}
      </button>
      <div class="profile__dropdown dropdown-menu dropdown-menu-right">
        <ul>
          <li>
            <a :href="backendPrefix + 'profile-edit'">
              <i class="fas fa-user-edit fa-fw"></i>
              {{ labels['action.edit_profile'] }}
            </a>
          </li>
          <li>
            <a :href="backendPrefix + 'logout'">
              <i class="fas fa-sign-out-alt fa-fw"></i>
              {{ labels['action.logout'] }}
            </a>
          </li>
          <hr>
          <li>
            <a href="https://boltcms.io/" target="_blank">
              <i class="fas fa-globe-americas fa-fw"></i>
              {{ labels['about.visit_bolt'] }}
            </a>
          </li>
          <li>
            <a href="https://docs.bolt.cm/" target="_blank">
              <i class="fas fa-book fa-fw"></i>
              {{ labels['about.bolt_documentation'] }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
const tinycolor = require('tinycolor2');

export default {
  name: 'Toolbar',
  props: {
    siteName: String,
    menu: Array,
    labels: Object,
    backendPrefix: RegExp,
    filterValue: String,
  },
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
