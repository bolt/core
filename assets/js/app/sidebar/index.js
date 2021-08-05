import Vue from 'vue';
import store from './store';
import Sidebar from './Components/Sidebar.vue';

const id = 'bolt--sidebar';

if (document.getElementById(id)) {
    new Vue({
        store,
        el: '#' + id,
        name: 'BoltSidebar',
        components: {
            'admin-sidebar': Sidebar,
        },
    });
}
