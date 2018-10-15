<template>
  <nav class="nav flex-column nav-fill">
    <div class="logo">
      <h2>Bolt</h2>
    </div>
    <!-- TODO: Maybe we need to parse the data somewhere else -->
    <template v-for="menuitem in JSON.parse(sidebarmenudata)">

      <!-- separators -->
      <hr v-if="menuitem.type"/>
      <div v-if="menuitem.type" class="nav-item separator">
          <i class="fas" :class="menuitem.icon_one"></i>
        {{ menuitem.name }}
      </div>

      <!-- Non-contenttype links -->
      <a v-else-if="!menuitem.contenttype" :href="menuitem.link" class="nav-item nav-link" :key="menuitem.id">
        <span v-if="!menuitem.type" class="fa-stack">
          <i class="fas fa-square fa-stack-2x"></i>
          <i class="fas fa-stack-1x" :class="menuitem.icon_one"></i>
        </span>
        {{ menuitem.name }}
      </a>

      <!-- Contenttypes -->


      <div v-else="" class="dropdown" :key="menuitem.id" :class="[ menuitem.active ? 'current' : '' ]">
        <a :href="menuitem.link" button class="nav-item nav-link dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-trigger="hover">
          <span class="fa-stack">
            <i class="fas fa-square fa-stack-2x"></i>
            <i class="fas fa-stack-1x" :class="menuitem.icon_many"></i>
          </span>
          {{ menuitem.name }}
        </a>

        <!-- that are not Singleton -->
        <div v-if="!menuitem.singleton" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <a v-for="record in getRecordsPerContenttype(menuitem.contenttype)" :key="record.id" class="dropdown-item" :href="'/bolt/edit/' + record.id">
            <i class="fas icon" :class="menuitem.icon_one"></i>
            {{ record.magictitle }}
          </a>
          <div class="btn-group" role="group">
          <a class="btn btn-light btn-sm" :href="'/bolt/content/' + menuitem.contenttype">
            <i class="fas icon" :class="menuitem.icon_one"></i>
            View {{ menuitem.name }}
          </a>
          <a class="btn btn-light btn-sm" :href="'/bolt/edit/' + menuitem.contenttype">
             <i class="fas fa-plus icon"></i>
            New {{ menuitem.name }}
          </a>
          </div>
        </div>

      </div>
    </template>
  </nav>
</template>

<script>
import ContentAPI from "../service/api/content";

export default {
  name: "sidebar",
  props: ["sidebarmenudata"],
  data() {
    return {
      message: "",
      loading: true,
      records: []
    };
  },
  methods: {
    getRecordsPerContenttype(contenttypeslug) {
      if (localStorage.getItem("records-" + contenttypeslug) === null) {
        return this.records[contenttypeslug];
      } else {
        return ContentAPI.getRecords(contenttypeslug);
      }
    }
  },
  created() {
    let sidebarmenudata = JSON.parse(this.sidebarmenudata);

    for (let i = 0; i < sidebarmenudata.length; i++) {
      if (sidebarmenudata[i].contenttype) {
        setTimeout(() => {
          ContentAPI.fetchRecords(sidebarmenudata[i].contenttype)
            .then(records => {
              this.$set(this.records, sidebarmenudata[i].contenttype, records);
            })
            .catch(error => console.log(error));
        }, 200);
      }
    }
  }
};
</script>

<style lang="scss">
@import "../../scss/settings";

nav.flex-column {
  background-color: $sidebar-background;

  hr {
    border-top-width: 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    margin: 0;
  }

  .logo {
    color: #fff;
    background: $sidebar-background;
    text-align: center;
    margin: 1rem;

    h2 {
      font-size: 36px;
  }

  }

  .nav-item {
    color: #ddd !important;
    padding-top: 0.6rem;
    padding-bottom: 0.6rem;
    text-align: left;

    a {
      color: #ddd !important;
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

    &.active,
    &.current {
      background-color: $sidebar-active !important;
      color: #fff !important;

      > a {
        color: #fff !important;
      }
    }
  }

  .dropdown-menu {
    transform: translateX(140px) !important;
    padding-bottom: 0;

    a {
      padding: 0.25rem 0.75rem;
    }

    .btn-group {
      width: 100%;
      background-color: #EEE;
      border-top: 1px solid #DDD;
      margin-top: 0.5rem;
      display: flex
    }

    .btn {
      background-color: #EEE;
      border: 0;
      flex: 1;
      padding: 0.5rem 0;
    }
    .btn:hover {
      background-color: #CCC;
      border: 0;
    }


  }

}
</style>
