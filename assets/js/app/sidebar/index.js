import Vue from 'vue';
import { createPinia, PiniaVuePlugin } from 'pinia';
import Sidebar from './Components/Sidebar.vue';

Vue.use(PiniaVuePlugin);

const id = 'bolt--sidebar';

if (document.getElementById(id)) {
    new Vue({
        pinia: createPinia(),
        el: '#' + id,
        name: 'BoltSidebar',
        components: {
            'admin-sidebar': Sidebar,
        },
    });
}
