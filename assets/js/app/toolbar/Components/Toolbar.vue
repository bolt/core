<template>
  <div class="bg-primary text-light"
    role="toolbar"
  >
    <b-navbar variant="primary p-0">
      <div class="mr-auto">
        <b-dropdown variant="primary" no-caret>
          <template slot="button-content">
            <div class="svg-brand">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 91 79">
                <g fill="white" fill-rule="evenodd">
                  <path
                    d="M22.4439 78.14003L0 38.98236 22.75 0l45.19015.17305L90.3848 39.33448 67.63857 78.32135l-45.18714-.17305-.00752-.00827zm4.97036-7.58863l.00588.00667 35.31413.13936 17.77638-31.39679L62.96997 7.76328l-35.31649-.13935L9.87415 39.01708 27.41426 70.5514z"
                  />
                  <path
                    fill-rule="nonzero"
                    d="M31.58678 31.6h7.52066v15.04762h-7.52066z"
                  />
                  <path
                    fill-rule="nonzero"
                    d="M37.5657 35.3619h22.56198v7.52381H37.5657z"
                  />
                </g>
              </svg>
            </div>
          </template>
          <b-dropdown-item href="https://bolt.cm/" target="_blank">
            Visit Bolt.cm
          </b-dropdown-item>
          <b-dropdown-item href="https://docs.bolt.cm/" target="_blank">
            {{labels['about.bolt_documentation']}}
          </b-dropdown-item>
        </b-dropdown>
      </div>
      <div>
        <b-nav variant="primary">
          <b-dropdown variant="primary" no-caret>
            <template slot="button-content">
              <i class="fas fa-magic mr-2"></i><span class="d-none d-sm-inline">{{ labels['action.create_new'] }}</span>
            </template>
            <b-dropdown-item v-for="(item, index) in createMenu" :key="index" :href="item.link_new">
              {{ item.singular_name }}
            </b-dropdown-item>
          </b-dropdown>
          <b-button variant="primary">
              <i class="fas fa-globe-americas mr-2"></i><span class="d-none d-sm-inline">{{ labels['action.visit_site'] }}: {{ siteName }}</span>
          </b-button>
          <b-dropdown variant="primary" no-caret right>
            <template slot="button-content">
              <i class="fas fa-user mr-2"></i><span class="d-none d-sm-inline">{{ labels['general.greeting'] }}</span>
            </template>
            <b-dropdown-item :href="backend_prefix + 'profile-edit'">
              {{ labels['action.edit_profile'] }}
            </b-dropdown-item>
            <b-dropdown-item :href="backend_prefix + 'logout'">
              {{ labels['action.logout'] }}
            </b-dropdown-item>
          </b-dropdown>
        </b-nav>
      </div>
    </b-navbar>
  </div>
</template>

<script>

export default {
  name: 'Toolbar',
  props: ['siteName', 'menu', 'labels', 'backend_prefix'],
  computed: {
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
