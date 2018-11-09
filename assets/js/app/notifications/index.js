import Vue from 'vue'

import Notification from './Components/Notification'

document.addEventListener('DOMContentLoaded', () => new Vue({
  el: '#notification',
  name: 'notification',
  components: {
    Notification
  },
  data () {
    const {dataset} = document.querySelector(this.$options.el)
    return {
      type: dataset.type,
      message: dataset.message,
      label: dataset.closingLabel,
      time: dataset.duration
    }
  },
  render (createElement) {
    return createElement('notification', {
      props: {
        type: this.type,
        message: this.message,
        closingLabel: this.label,
        duration: this.time
      }
    })
  }
}))
