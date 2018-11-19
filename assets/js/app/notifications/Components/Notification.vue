<template>
    <div class="admin__notification-message">
        <transition name="notification" v-on:after-enter="show = false" :duration="duration">
            <div v-show="show" class="notification" :data-type="`is-${type}`" role="alert">
                <div v-html="message" class="notification__message"></div>
                <button type="button" class="notification__close" aria-label="Close" @click="show = false">
                    {{ closingLabel }}
                </button>
            </div>
        </transition>
    </div>
</template>

<script>
    import eventHub from "../eventhub";

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
                    default: "danger",
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
        mounted() {
            this.show = true;
        },
        data: () => {
            return {
                show: false
            }
        },
        created() {
            const self = this;
            eventHub.$on('showMessage', (message) => {
                self.message = message.message;
                self.type = message.type;
                self.closingLabel = message.closingLabel;
                self.duration = message.duration;
                this.show = true;
            })
        }
    }
</script>
