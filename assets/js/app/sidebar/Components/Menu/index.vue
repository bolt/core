<template>
  <ul class="admin__sidebar--menu">
    <li v-for="(item, index) in menu" :key="index">
      <p 
        class="admin__sidebar--separator" 
        v-if="item.type === 'separator'"
      >
        {{item.name}}
      </p>
      <a 
        class="admin__sidebar--link" 
        :href="singleton(item)" 
        v-else-if="item.singleton"
      >
        <i class="fas mr-2 link--icon" :class="item.icon"></i><span class="link--text">{{item.name}}</span>
      </a>
      <a 
        :href="item.link" class="admin__sidebar--link" 
        :class="{ 'has-menu': item.submenu !== null || item.contentType !== null }"
        v-else
      >
        <i class="fas mr-2 link--icon" :class="item.icon"></i><span class="link--text">{{item.name}}</span>
        <template v-if="item.submenu !== null || item.contentType !== null">
          <i class="fas fa-caret-right link--caret"></i>
          <sub-menu
            :item="item"
          ></sub-menu>
        </template>
      </a>
    </li>
  </ul>
</template>

<script>
import SubMenu from './_SubMenu';

export default {
  name: "sidebar-menu",
  props: ["menu"],
  components: {
    "sub-menu": SubMenu
  },
  methods: {
    singleton(item){
      if(item.submenu !== null){
        return item.submenu[0].editLink
      } else{
        return item.link_new
      }
    }
  },
};
</script>