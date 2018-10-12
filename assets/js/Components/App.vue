<template>
    <div class="container">
        <h1>Hello</h1>
    </div>
</template>

<script>
import router from "../router";

export default {
  name: "app",
  beforeMount() {
    let vueRouting = this.$parent.$el.attributes["data-vue-routing"].value;
    router.push({ path: vueRouting });
  }
};
</script>
