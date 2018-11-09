<template>
  <nav class="admin__header--topbar">
      <h2 class="admin__header--title" v-html="title"></h2>
  </nav>
</template>

<script>
export default {
  name: "bolt-title",
  props: ["title"]
};
</script>