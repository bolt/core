<template>
  <nav class="admin__header--topbar">
      <h2 class="admin__header--title" v-html="title"></h2>
  </nav>
</template>

<script>
export default {
  name: "admin-header",
  props: ["title"]
};
</script>