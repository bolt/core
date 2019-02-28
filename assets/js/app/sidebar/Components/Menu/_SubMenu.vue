<template>
  <ul class="link--menu">
    <li v-if="item.link_new !== null" class="link--create">
      <a :href="item.link_new">
        <i class="fas fa-fw fa-magic mr-2"></i
        ><span>New {{ item.singular_name }}</span>
      </a>
    </li>
    <!-- eslint-disable vue/no-use-v-if-with-v-for -->
    <li
      v-for="(subitem, index) in item.submenu"
      v-if="item.submenu !== null"
      :key="index"
    >
      <!-- eslint-enable -->
      <a :href="subitem.editLink">
        <i class="fas fa-fw mr-2" :class="subitem.icon"></i>
        <!-- eslint-disable-next-line vue/no-v-html -->
        <span v-html="subitem.name"></span>
      </a>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'SubMenu',
  props: ['item'],
};
</script>
