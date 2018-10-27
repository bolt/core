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
          v-for="(item, index) in content()"
          :key="index"
          :href="`/bolt/edit/${item.slug}`"
          v-if="!item.singleton"
        >
          {{item.singular_name}}
        </a>
      </div>
    </div>
    <div>
      <ul class="admin__sidebar--menu">
        <li v-for="(item, index) in menu" :key="index">
          <p class="admin__sidebar--separator" v-if="item.type === 'separator'">
            {{item.name}}
          </p>
          <a class="admin__sidebar--link" :href="item.link" v-else-if="item.singleton">
            <i class="fas mr-2 link--icon" :class="item.icon"></i><span class="link--text">{{item.name}}</span>
          </a>
          <a :href="item.link" class="admin__sidebar--link" :class="{ 'has-menu': item.submenu !== null || item.contenttype !== null }" v-else>
            <i class="fas mr-2 link--icon" :class="item.icon"></i><span class="link--text">{{item.name}}</span>

            <template v-if="item.submenu !== null || item.contenttype !== null">
              <i class="fas fa-caret-right link--caret"></i>
              <ul class="link--menu">
                <li v-if="item.link_new !== null" class="link--create">
                  <a :href="item.link_new">
                    <i class="fas fa-plus mr-2"></i><span>New {{item.singular_name}}</span>
                  </a>
                </li>
                <li v-if="item.submenu !== null" v-for="(record, index) in item.submenu" :key="index">
                  <a :href="record.editlink">
                    <i class="fas mr-2" :class="record.icon"></i><span v-html="record.name"></span>
                  </a>
                </li>
              </ul>
            </template>

          </a>
        </li>
      </ul>
    </div>
    <button class="admin__sidebar--slim" @click="slimMenu()"><i class="fas fa-arrows-alt-h"></i></button>
    <footer class="admin__sidebar--footer">
      Bolt {{version}}
    </footer>
  </nav>
</template>

<script>
import ContentAPI from "../../service/api/content";
const admin = document.querySelector('.admin');

export default {
  name: "admin-sidebar",
  props: ["brand", "menu", "version"],
  created() {
    const size = localStorage.getItem('admin-sidebar-size');
    if (size !== null && size === 'slim'){
      this.size = 'slim'
    }
  },
  data: () => {
    return {
      size: "normal",
    };
  },
  watch: {
    size(){
      if(this.size === 'normal'){
        admin.classList.remove('is-slim');
      } else {
        admin.classList.add('is-slim');
      }
      localStorage.setItem('admin-sidebar-size', this.size);
    }
  },
  methods: {
    slimMenu(){
      if(this.size === "normal"){
        this.size = "slim"
      } else {
        this.size = "normal"
      }
    },
    content(){
      return this.menu.filter(item => item.contenttype !== null)
    }
  },
};
</script>