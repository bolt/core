import { mapState } from 'pinia';
import { useGeneralStore } from '../store';

export default {
    computed: {
        ...mapState(useGeneralStore, ['type']),
    },
};
