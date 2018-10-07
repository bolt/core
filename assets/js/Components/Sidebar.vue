<template>
  <div class="ui vertical inverted borderless small blue menu">
    <div class="header item logo">
      <h2>Bolt</h2>
    </div>
    <!-- TODO: Maybe we need to parse the data somewhere else -->
    <template v-for="menuitem in JSON.parse(sidebarmenudata)">
      <hr v-if="menuitem.type"/>
      <div v-if="menuitem.type" class="item">
        <span class="fa-stack">
          <i class="fas" :class="menuitem.icon"></i>
        </span>
        {{ menuitem.name }}
      </div>
      <a v-else-if="!menuitem.contenttype" :href="menuitem.link" class="item" :key="menuitem.id">
        <span v-if="!menuitem.type" class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon"></i>
        </span>
        {{ menuitem.name }}
      </a>
      <div v-else="" class="ui dropdown item left pointing floating" :key="menuitem.id">
        <i v-if="!menuitem.singleton" class="dropdown icon"></i>
        <a :href="menuitem.link">
        <span class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon"></i>
        </span>
          {{ menuitem.name }}
        </a>

        <div v-if="!menuitem.singleton" class="menu">
          <a class="item" :href="'/bolt/content/' + menuitem.contenttype">
            <i class="fas icon" :class="menuitem.icon"></i>
            View {{ menuitem.name }}
          </a>
          <a class="item" :href="'/bolt/edit/' + menuitem.contenttype">
             <i class="fas fa-plus icon"></i>
            New {{ menuitem.name }}
          </a>
          <div class="divider"></div>
          <a v-for="record in getRecordsPerContenttype(menuitem.contenttype)" :key="record.id" class="item" :href="'/bolt/edit/' + record.id">
            <i class="fas icon" :class="menuitem.icon"></i>
            {{ record.magictitle }}
          </a>
        </div>

      </div>
    </template>
  </div>
</template>

<script>
    import ContentAPI from '../service/api/content';

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