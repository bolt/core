import { createApp } from 'vue';
import store from './store';
import Sidebar from './Components/Sidebar.vue';

const id = 'bolt--sidebar';
const sidebar = {
    data() {
        return{
            store,
            el: '#' + id,
            name: 'BoltSidebar',
            components: {
                'admin-sidebar': Sidebar,
            },
        }
    }
}

    const app = createApp(sidebar)
    app.component('admin-sidebar', Sidebar)
    app.mount('#' + id);
