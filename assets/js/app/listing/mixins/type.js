import store from '../store';

export default {
    computed: {
        type() {
            return store.getters['general/getType'];
        },
    },
};
