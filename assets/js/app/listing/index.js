import { createApp } from 'vue';
/**
 * VueX Store
 */
import store from './store';
/**
 * Components
 */
import Records from './Components/Records';
import Filter from './Components/Filter';
import Table from './Components/Table';
import SelectBox from './Components/SelectBox';

/**
 * Register Components
 */
const id = 'listing';
const listing = {
    data() {
        return {
            store,
            el: '#' + id,
            name: 'BoltListing',
            components: {
                'listing-records': Records,
                'listing-filter': Filter,
                'listing-table': Table,
                'listing-select-box': SelectBox,
            },
        }
    }
}

if (document.getElementById(id)) {
    const app = createApp(listing);
    app.component('ListingRecords', Records)
    app.component('ListingFilter', Filter)
    app.component('ListingTable', Table)
    app.component('ListingSelectBox', SelectBox)
    app.mount('#' + id);
}
