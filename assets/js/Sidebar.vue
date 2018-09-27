<template>
  <div class="ui vertical inverted borderless small blue menu">
    <div class="header item logo">
      <h2>Bolt</h2>
    </div>
    <!-- TODO: Maybe we need to parse the data somewhere else -->
    <template v-for="menuitem in JSON.parse(sidebarmenudata)">
      <a v-if="!menuitem.contenttype" :href="menuitem.link" class="item" :key="menuitem.id">
        <span class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon"></i>
        </span>
        {{ menuitem.name }}
      </a>
      <div v-else="" class="ui dropdown item" :key="menuitem.id">
        <!-- {{menuitem.contenttype}} -->
        <i class="dropdown icon"></i>
        <span class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon"></i>
        </span>
        {{ menuitem.name }}
        <div class="menu">
          <!-- TODO: Print Links to latest edited record per contenttype -->
          <div class="header">{{ menuitem.name }}</div>
            <a v-for="record in getRecordsPerContenttype(menuitem.contenttype)" :key="record.id" class="item" :href="'/bolt/edit/' + record.id">
              {{ record.magictitle }}
            </a>
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
                records: [],
            };
        },
        methods: {
          getRecordsPerContenttype(contenttypeslug) {
            if(localStorage.getItem('records-' + contenttypeslug) === null) {
              return this.records[contenttypeslug];
            } else {
              return ContentAPI.getRecords(contenttypeslug);
            }
          },
        },
        created() {

          let sidebarmenudata = JSON.parse(this.sidebarmenudata)

          for(let i = 0; i < sidebarmenudata.length; i++) {
            if(sidebarmenudata[i].contenttype) {
              ContentAPI.fetchRecords(sidebarmenudata[i].contenttype)
                .then( records => {
                  this.$set(this.records, sidebarmenudata[i].contenttype, records)
                })
                .catch(error => console.log(error))
            }
          }
        }
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