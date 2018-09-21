import ContentAPI from '../service/api/content';

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        content: [],
    },
    getters: {
        isLoading (state) {
            return state.isLoading;
        },
        hasError (state) {
            return state.error !== null;
        },
        error (state) {
            return state.error;
        },
        hasContent (state) {
            return state.content.length > 0;
        },
        content (state) {
            return state.content;
        },
    },
    mutations: {
        ['FETCHING_CONTENT'](state) {
            state.isLoading = true;
            state.error = null;
            state.content = [];
        },
        ['FETCHING_CONTENT_SUCCESS'](state, content) {
            state.isLoading = false;
            state.error = null;
            state.content = content;
        },
        ['FETCHING_CONTENT_ERROR'](state, error) {
            state.isLoading = false;
            state.error = error;
            state.content = [];
        },
    },
    actions: {
        fetchContent ({commit}, type) {
            commit('FETCHING_CONTENT');
            // if exists in localStorage, serve that
            // state.content['fefef'] -> res.data
            // ?refresh=1 do real call

            return ContentAPI.getAll(type)
                .then(res => commit('FETCHING_CONTENT_SUCCESS', res.data))
                .catch(err => commit('FETCHING_CONTENT_ERROR', err))
            ;
        },
    },
}
