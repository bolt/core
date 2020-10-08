import Vue from 'vue';
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

if (document.getElementById(id)) {
    new Vue({
        store,
        el: '#' + id,
        name: 'BoltToolbar',
        components: {
            'admin-toolbar': Toolbar,
        },
    });
}
