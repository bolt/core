import Vue from 'vue';
/**
 * Pinia
 */
import { createPinia, PiniaVuePlugin } from 'pinia';
/**
 * Components
 */
import Toolbar from './Components/Toolbar.vue';

Vue.use(PiniaVuePlugin);

/**
 * Register Components
 */
const id = 'toolbar';

if (document.getElementById(id)) {
    new Vue({
        pinia: createPinia(),
        el: '#' + id,
        name: 'BoltToolbar',
        components: {
            'admin-toolbar': Toolbar,
        },
    });
}
