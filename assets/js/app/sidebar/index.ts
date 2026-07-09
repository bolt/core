import { createApp } from 'vue';
import { createPinia } from 'pinia';
import Sidebar from './Components/Sidebar.vue';

const id = 'bolt--sidebar';

if (document.getElementById(id)) {
    const app = createApp({
        name: 'BoltSidebar',
    });
    app.config.compilerOptions.whitespace = 'preserve';

    const pinia = createPinia();
    app.use(pinia);

    app.component('admin-sidebar', Sidebar);

    app.mount('#' + id);
}
