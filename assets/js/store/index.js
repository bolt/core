import Vue from 'vue';
import Vuex from 'vuex';
import ContentModule from './content';

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        content: ContentModule,
    }
});