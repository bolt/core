import { createApp } from 'vue';
import { createPinia } from 'pinia';
/**
 * Components
 */
import Records from './Components/Records.vue';
import Filter from './Components/Filter.vue';
import Table from './Components/Table/index.vue';
import SelectBox from './Components/SelectBox.vue';

/**
 * Register Components
 */
const id = 'listing';

if (document.getElementById(id)) {
    const app = createApp({
        name: 'BoltListing',
    });
    app.config.compilerOptions.whitespace = 'preserve';

    const pinia = createPinia();
    app.use(pinia);

    app.component('listing-records', Records);
    app.component('listing-filter', Filter);
    app.component('listing-table', Table);
    app.component('listing-select-box', SelectBox);

    app.mount('#' + id);
}
