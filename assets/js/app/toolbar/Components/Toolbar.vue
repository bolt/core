<template>
  <div class="admin__toolbar--body" :class="contrast ? 'is-light':'is-dark'" role="toolbar">
    <div class="toolbar--item is-brand">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 91 79">
        <g fill-rule="evenodd">
          <path d="M22.4439 78.14003L0 38.98236 22.75 0l45.19015.17305L90.3848 39.33448 67.63857 78.32135l-45.18714-.17305-.00752-.00827zm4.97036-7.58863l.00588.00667 35.31413.13936 17.77638-31.39679L62.96997 7.76328l-35.31649-.13935L9.87415 39.01708 27.41426 70.5514z"/>
          <path fill-rule="nonzero" d="M31.58678 31.6h7.52066v15.04762h-7.52066z"/>
          <path fill-rule="nonzero" d="M37.5657 35.3619h22.56198v7.52381H37.5657z"/>
        </g>
      </svg>
      <ul class="toolbar--menu">
        <li><a href="https://bolt.cm/" target="_blank">bolt.cm</a></li>     
        <li><a href="https://docs.bolt.cm/" target="_blank">{{labels['about.bolt_documentation']}}</a></li>      
      </ul>
    </div>
    <div class="toolbar--item is-new">
      <i class="fas fa-magic mr-2"></i>{{labels['action.create_new']}}
      <ul class="toolbar--menu">
        <li 
          v-for="(item, index) in createMenu" 
          :key="index" 
          v-if="!item.singleton || item.singleton && item.submenu.length < 1"
        >
          <a :href="item.link_new">{{item.singular_name}}</a>
        </li>          
      </ul>
    </div>
    <div class="toolbar--item is-site">
      <i class="fas fa-globe-americas mr-2"></i>{{siteName}}
      <ul class="toolbar--menu">
        <li>
          <a href="/" target="_blank">{{labels['action.visit_site']}}</a>
        </li>            
      </ul>
    </div>
    <div class="toolbar--item is-profile">
      <i class="fas fa-user mr-2"></i>{{labels['general.greeting']}}
      <ul class="toolbar--menu">
        <li>
          <a href="/bolt/profile-edit">{{labels['action.edit_profile']}}</a>
        </li>    
        <li>
          <a href="/bolt/logout">{{labels['action.logout']}}</a>
        </li>         
      </ul>
    </div>
  </div>
</template>

<script>
const tinycolor = require("tinycolor2");

export default {
  name: "toolbar",
  props: ["siteName", "menu", "labels"],
  created(){
    const color = getComputedStyle(document.body).getPropertyValue('--admin-toolbar');
    this.$store.dispatch('general/toolbarColor', color);
  },
  computed:{
    contrast(){
      const color = tinycolor(this.toolbarColor);
      return color.isLight();
    },
    createMenu(){
      return this.menu.filter(item => item.contentType !== null)
    },
    toolbarColor(){
      return this.$store.getters['general/toolbarColor']
    }
  }
};
</script>