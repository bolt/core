import { createApp } from 'vue';
import { createPinia } from 'pinia';
/**
 * Components
 */
import Toolbar from './Components/Toolbar.vue';

/**
 * Register Components
 */
const id = 'toolbar';

if (document.getElementById(id)) {
    const app = createApp({
        name: 'BoltToolbar',
    });
    app.config.compilerOptions.whitespace = 'preserve';

    const pinia = createPinia();
    app.use(pinia);

    app.component('admin-toolbar', Toolbar);

    app.mount('#' + id);
}
