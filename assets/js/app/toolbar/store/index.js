import { createApp } from 'vue';
import Vuex from 'vuex';
import general from './modules/general/';

createApp().use(Vuex);

export default new Vuex.Store({
    modules: {
        general,
    },
});
