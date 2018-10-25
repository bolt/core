<template>
  <nav class="admin__sidebar--nav">
    <a class="admin__sidebar--brand" href="/bolt">
      <img :src="brand" alt="Bolt Four">
    </a>
    <div class="admin__sidebar--create">
      <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-magic mr-2"></i> New
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a  
          class="dropdown-item" 
          v-for="(item, index) in menuLinks('Content')" 
          :key="index" 
          :href="`/bolt/edit/${item.slug}`" 
          v-if="!item.singleton"
        >
          {{item.singular_name}}
        </a>
      </div>
    </div>
    <div>
      <!-- loop through menu seperators -->
      <div v-for="(item, index) in menuSeparator" :key="index">
        <p class="admin__sidebar--separator">
          {{item.name}}
        </p>
        <ul class="admin__sidebar--menu">
          <!-- loop through menu items matching seperator name -->
          <li v-for="(item, index) in menuLinks(item.name)" :key="index">
              <template v-if="item.singleton">
                <a :href="item.records[0].editlink" class="admin__sidebar--link">
                  <i class="fas mr-2 link--icon" :class="item.icon_one"></i><span class="link--text">{{item.name}}</span>
                </a>
              </template>
              <template v-else>
                <a :href="item.link" class="admin__sidebar--link" :class="{'has-menu': item.records !== null}">
                  <i class="fas mr-2 link--icon" :class="item.icon_one"></i><span class="link--text">{{item.name}}</span>
                  <!-- loop through menu item records -->
                  <template v-if="item.records !== null">
                    <i class="fas fa-caret-right link--caret"></i>
                    <ul class="link--menu">
                      <!-- create new record -->
                      <li v-if="item.contenttype !== null" class="link--create">
                        <a :href="`/bolt/edit/${item.slug}`">
                          <i class="fas fa-plus mr-2"></i><span>New {{item.singular_name}}</span>
                        </a>
                      </li>
                      <li v-for="(record, index) in item.records" :key="index">
                        <a :href="record.editlink">
                          <i class="fas mr-2" :class="item.icon_one"></i><span>{{record.title}}</span>
                        </a>
                      </li>
                    </ul>
                    <!--  end loop -->
                  </template>
                </a>
              </template>
          </li>
          <!--  end loop -->
        </ul>
      </div>
      <!--  end loop -->
    </div>
    <button class="admin__sidebar--slim" @click="slimMenu()"><i class="fas fa-arrows-alt-h"></i></button>
    <footer class="admin__sidebar--footer">
      Bolt {{version}}
    </footer>
  </nav>
</template>

<script>
import ContentAPI from "../../service/api/content";

export default {
  name: "admin-sidebar",
  props: ["brand", "menu", "version"],
  mounted(){
    console.log(this.menu);
  },
  methods: {
    slimMenu(){
      const admin = document.querySelector('.admin');
      const sidebar = document.querySelector('.admin__sidebar');
      admin.classList.toggle('is-slim')
      sidebar.classList.toggle('is-slim')
    },
    menuLinks(type){
      if(type === 'Content'){
        return this.menu.filter(item => item.contenttype !== null)
      }
      if(type === 'Settings'){
        return this.menu.filter(item => item.contenttype === null && item.type !== 'separator' && item.name !== 'Dashboard')
      }
    }
  },
  computed: {
    menuSeparator(){
      return this.menu.filter(item => item.type === 'separator')
    },
  }
};
</script>