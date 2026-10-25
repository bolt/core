import Vue from 'vue';
/**
 * Pinia
 */
import { createPinia, PiniaVuePlugin } from 'pinia';
/**
 * Components
 */
import Records from './Components/Records';
import Filter from './Components/Filter';
import Table from './Components/Table';
import SelectBox from './Components/SelectBox';

Vue.use(PiniaVuePlugin);

/**
 * Register Components
 */
const id = 'listing';

if (document.getElementById(id)) {
    new Vue({
        pinia: createPinia(),
        el: '#' + id,
        name: 'BoltListing',
        components: {
            'listing-records': Records,
            'listing-filter': Filter,
            'listing-table': Table,
            'listing-select-box': SelectBox,
        },
    });
}
