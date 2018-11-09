<template>
  <div class="admin__notification-message">
  <transition name="notification">
    <div class="notification" :data-type="`is-${type}`" role="alert" v-if="!close">
      <p v-html="message"></p>
      <button type="button" class="notification--close" aria-label="Close" @click="close = true">
        {{ closingLabel }}
      </button>
    </div>
  </transition>
  </div>
</template>

<script>
  export default {
    name: 'bolt-notification',
    props:
      {
        message: {
          type: String,
          required: true,
          default: ""
        },
        type: {
          type: String,
          required: true,
          default: "error",
        },
        closingLabel: {
          type: String,
          required: true,
          default: "action.close_alert"
        },
        duration: {
          type: String,
          required: false,
          default: "300"
        }
      },
    mounted () {
      setTimeout(() => this.close = false, 0)
      if (this.duration !== false) {
        setTimeout(() => this.close = true, this.duration)
      }
    },
    data: () => {
      return {
        close: true
      }
    }
  }
</script>
