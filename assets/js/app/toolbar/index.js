import { createApp } from 'vue';
/**
 * VueX Store
 */
import store from './store';
/**
 * Components
 */
import Toolbar from './Components/Toolbar.vue';

/**
 * Register Components
 */
const id = 'toolbar';
const toolbar = {
    data() {
        return {
            store,
            el: '#' + id,
            name: 'BoltToolbar',
            components: {
                'admin-toolbar': Toolbar,
            },
        };
    },
};
const app = createApp(toolbar);

app.component('AdminToolbar', Toolbar);
app.mount('#' + id);
