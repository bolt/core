<template>
    <nav class="listing__filter">
        <ul class="listing__filter--controls">
            <!-- Check all checkbox -->
            <li v-if="type !== 'dashboard'" class="control--checkbox">
                <div v-if="!sorting" class="form-check">
                    <input
                        id="selectAll"
                        :value="selectAll"
                        class="form-check-input"
                        type="checkbox"
                        @click="enableSelectAll(!selectAll)"
                    />
                    <label class="form-check-label form-label" for="selectAll" @click="enableSelectAll(!selectAll)">
                        <span class="sr-only">{{ labels.select_all }}</span>
                    </label>
                </div>
            </li>

            <!-- Expanded mode -->
            <li class="control--left">
                <button
                    class="control--button"
                    :class="{ 'is-selected': size === 'normal' }"
                    :title="labels.button_expanded"
                    @click="changeSize('normal')"
                >
                    <i class="fas fa-align-justify fa-fw"></i>
                    <span class="sr-only">{{ labels.button_expanded }}</span>
                </button>
            </li>

            <!-- Compact mode -->
            <li>
                <button
                    class="control--button"
                    :class="{ 'is-selected': size === 'small' }"
                    :title="labels.button_compact"
                    @click="changeSize('small')"
                >
                    <i class="fas fa-grip-lines fa-fw"></i>
                    <span class="sr-only">{{ labels.button_compact }}</span>
                </button>
            </li>

            <!-- Drag and drop -->
            <!--
      <li v-if="type !== 'dashboard'">
        <button
          class="control--button"
          :class="{ 'is-active': sorting }"
          @click="enableSorting(!sorting)"
        >
          <i class="fas" :class="sorting ? 'fa-check-circle' : 'fa-sort'"></i>
        </button>
      </li>
      -->
        </ul>
    </nav>
</template>

<script>
import type from '../mixins/type';

export default {
    name: 'ListingFilter',
    mixins: [type],
    props: {
        labels: Object,
    },
    computed: {
        size() {
            return this.$store.getters['general/getRowSize'];
        },
        sorting() {
            return this.$store.getters['general/getSorting'];
        },
        selectAll() {
            return this.$store.getters['selecting/selectAll'];
        },
    },
    watch: {
        sorting() {
            if (this.sorting) this.$store.dispatch('selecting/selectAll', false);
        },
    },
    created() {
        const size = localStorage.getItem('listing-row-size');
        if (size !== null) this.$store.dispatch('general/setRowSize', size);
    },
    methods: {
        enableSorting(arg) {
            this.$store.dispatch('general/setSorting', arg);
        },
        enableSelectAll(arg) {
            this.$store.dispatch('selecting/selectAll', arg);
        },
        changeSize(size) {
            this.$store.dispatch('general/setRowSize', size);
            localStorage.setItem('listing-row-size', size);
        },
    },
};
</script>
