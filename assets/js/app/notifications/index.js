import Vue from 'vue'
import Notification from './Components/Notification'

document.addEventListener('DOMContentLoaded', () => {
    const notifications = Array.from(document.querySelectorAll(".notifications"));
    for (let notification of notifications) {
        new Vue({
            el: notification,
            name: 'notification',
            components: {
                Notification
            },
            data() {
                const {dataset} = notification
                return {
                    type: dataset.type,
                    message: dataset.message,
                    label: dataset.closingLabel,
                    time: dataset.duration
                }
            },
            render(createElement) {
                return createElement('notification', {
                    props: {
                        type: this.type,
                        message: this.message,
                        closingLabel: this.label,
                        duration: this.time
                    }
                })
            },
            /*
            Example for event-driven notifications
            created() {
                setTimeout(() => {
                    eventHub.$emit('showMessage', {
                        message: "hello world"
                    })
                }, 4000)
            }
             */
        })

    }
})