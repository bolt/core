<template>
  <div role="toolbar">

  </div>
</template>

<script>
module.exports = {
  name: "admin-toolbar",
  props: ["title", "content"],
  computed:{
    createMenu(){
      return this.content.filter(item => item.contenttype !== null)
    },
  }
};
</script>