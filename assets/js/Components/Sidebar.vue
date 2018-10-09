<template>
  <div class="ui vertical inverted borderless small menu">
    <div class="header item logo">
      <h2>Bolt</h2>
    </div>
    <!-- TODO: Maybe we need to parse the data somewhere else -->
    <template v-for="menuitem in JSON.parse(sidebarmenudata)">

      <!-- separators -->
      <hr v-if="menuitem.type"/>
      <div v-if="menuitem.type" class="item separator">
          <i class="fas" :class="menuitem.icon_one"></i>
        {{ menuitem.name }}
      </div>

      <!-- Non-contenttype links -->
      <a v-else-if="!menuitem.contenttype" :href="menuitem.link" class="item" :key="menuitem.id">
        <span v-if="!menuitem.type" class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon_one"></i>
        </span>
        {{ menuitem.name }}
      </a>

      <!-- Contenttypes -->
      <div v-else="" class="ui dropdown item left pointing floating" :key="menuitem.id" :class="[ menuitem.active ? 'current' : '' ]">
        <i v-if="!menuitem.singleton" class="dropdown icon"></i>
        <a :href="menuitem.link">
        <span class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon_many"></i>
        </span>
          {{ menuitem.name }}
        </a>

        <!-- that are not Singleton -->
        <div v-if="!menuitem.singleton" class="menu">
          <a class="item" :href="'/bolt/content/' + menuitem.contenttype">
            <i class="fas icon" :class="menuitem.icon_one"></i>
            View {{ menuitem.name }}
          </a>
          <a class="item" :href="'/bolt/edit/' + menuitem.contenttype">
             <i class="fas fa-plus icon"></i>
            New {{ menuitem.name }}
          </a>
          <div class="divider"></div>
          <a v-for="record in getRecordsPerContenttype(menuitem.contenttype)" :key="record.id" class="item" :href="'/bolt/edit/' + record.id">
            <i class="fas icon" :class="menuitem.icon_one"></i>
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

<style lang="scss">
@import '../../scss/settings';

.ui.small.vertical.menu {
  border-radius: 0;
  width: auto;
  font-size: 1rem;
  background-color: $sidebar-background;

  hr {
    border-top-width: 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    margin: 0;
  }

  .logo {
    color: #FFF;
    background: $sidebar-background;
    text-align: center;
    font-size: 36px;
    margin: 0;
  }

  .item {
      color: #DDD !important;
      padding-top: 0.6rem;
      padding-bottom: 0.6rem;

      a {
        color: #DDD !important;
      }

      .fa-stack {
        height: 2.3em;
        margin-right: 0.5rem;

        i:last-child {
            color: #444;
        }
      }

      & > i.dropdown.icon {
        margin-top: 10px;
      }

      &.separator {
          padding: 1rem 1rem 0.5rem;
          color: rgba(200, 200, 200, 0.5) !important;
          .fas {
            padding: 0 1.1rem 0 0.65rem;
          }
        }

      &.active, &.current {
          background-color: $sidebar-active !important;
          color: #FFF !important;

          > a {
            color:#FFF !important;
          }
      }
  }

  .menu.transition {
    margin-left: -28px;
    font-size: 0.9rem;

    a {
      padding: 0.5rem 1rem !important;
    }
  }
}
</style>