<template>
  <div class="admin__notification-message">
    <transition
      name="notification"
      :duration="duration"
      @after-enter="show = false"
    >
      <div
        v-show="show"
        class="notification"
        :data-type="`is-${type}`"
        role="alert"
      >
        <!-- eslint-disable-next-line vue/no-v-html -->
        <div class="notification__message" v-html="message"></div>
        <button
          type="button"
          class="notification__close"
          aria-label="Close"
          @click="show = false"
        >
          {{ closingLabel }}
        </button>
      </div>
    </transition>
  </div>
</template>

<script>
import eventHub from '../eventhub';

export default {
  name: 'BoltNotification',

  props: {
    message: {
      type: String,
      required: true,
      default: '',
    },
    type: {
      type: String,
      required: true,
      default: 'danger',
    },
    closingLabel: {
      type: String,
      required: true,
      default: 'action.close_alert',
    },
    duration: {
      type: String,
      required: false,
      default: '300',
    },
  },

  data: () => {
    return {
      show: false,
    };
  },

  mounted() {
    this.show = true;
  },

  created() {
    const self = this;
    eventHub.$on('showMessage', message => {
      self.message = message.message;
      self.type = message.type;
      self.closingLabel = message.closingLabel;
      self.duration = message.duration;
      this.show = true;
    });
  },
};
</script>
