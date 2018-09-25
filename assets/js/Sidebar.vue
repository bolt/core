<template>
  <div class="ui vertical inverted borderless small blue menu">
    <div class="header item logo">
      <h2>Bolt</h2>
    </div>
    <!-- TODO: Maybe we need to parse the data somewhere else -->
    <template v-for="menuitem in JSON.parse(sidebarmenudata)">
      <a :href="menuitem.link" class="item" v-if="!menuitem.contenttype" :key="menuitem.id">
        <span class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon"></i>
        </span>
        {{ menuitem.name }}
      </a>
      <div class="ui dropdown item" :key="menuitem.id" v-else="">
        <!-- {{menuitem.contenttype}} -->
        <i class="dropdown icon"></i>
        <span class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon"></i>
        </span>
        {{ menuitem.name }}
        <div class="menu">
          <!-- TODO: Print Links to latest edited record per contenttype -->
          <div class="header">Text Size</div>
          <a class="item">Small</a>
          <a class="item">Medium</a>
          <a class="item">Large</a>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
    import ContentAPI from './service/api/content';

    export default {
        name: 'sidebar',
        props: [
            'sidebarmenudata',
        ],
        data () {
            return {
                message: '',
                loading: true,
                records: []
            };
        },
        created () {
          // TODO: This data is already initialized somewhere else, Use it!!
            this.records = ContentAPI.getRecords('pages')

            ContentAPI.fetchRecords('pages')
                .then( records => {
                    this.records = records
                })
                .catch(error => console.log(error))
                .finally(() => {
                    this.loading = false
                });
        },
    }
</script>

<style>
.ui.small.vertical.menu {
  border-radius: 0;
  width: auto;
}

.ui.small.vertical.menu .logo {
  color: #FFF;
  background: #24455E;
  text-align: center;
  font-size: 36px;
  margin: 0;
}

.ui.inverted.blue.menu {
    background-color: rgba(0, 0, 0, 0.2);
}

.ui.inverted.blue.menu .active.item {
    background-color: rgba(255, 255, 255, 0.2) !important;
}
</style>