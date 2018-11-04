<template>
  <transition name="notification">
    <div class="notification" :data-type="`is-${type}`" role="alert" v-if="!close">
        <p v-html="message"></p>
        <button type="button" class="notification--close" aria-label="Close" @click="close = true">
            {{ labels['action.close_alert'] }}
        </button>
    </div>
  </transition>
</template>

<script>
export default {
  name: "bolt-notification",
  props: ["message", "type", "time", "labels"],
  mounted(){
    setTimeout(() => this.close = false, 0);
    if(this.time !== false){
      setTimeout(() => this.close = true, this.time);
    }
  },
  data: () => {
    return {
      close: true
    };
  },
};
</script>