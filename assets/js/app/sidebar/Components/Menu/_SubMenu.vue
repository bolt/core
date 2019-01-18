<template>
  <ul class="link--menu">
    <li v-if="item.link_new !== null" class="link--create">
      <a :href="item.link_new">
        <i class="fas fa-fw fa-magic mr-2"></i><span>New {{item.singular_name}}</span>
      </a>
    </li>
    <li v-if="item.submenu !== null" v-for="(record, index) in item.submenu" :key="index">
      <a :href="record.editLink || record.link">
        <i class="fas fa-fw mr-2" :class="record.icon"></i><span v-html="record.name"></span>
      </a>
    </li>
  </ul>
</template>

<script>
import SubMenu from './_SubMenu';

export default {
  name: "sub-menu",
  props: ["item"]
};
</script>