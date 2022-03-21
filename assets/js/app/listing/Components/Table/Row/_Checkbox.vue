<template>
    <div v-show="!sorting" key="checkbox" class="listing--checkbox">
        <div class="custom-control custom-checkbox">
            <input :id="`row-${id}`" v-model="selected" class="custom-control-input" type="checkbox" />
            <label class="custom-control-label" :for="`row-${id}`" @click="selected != !selected"></label>
        </div>
    </div>
</template>

<script>
import store from '../../../store';

export default {
    name: 'Checkbox',
    props: {
        id: Number,
    },
    data: () => {
        return {
            selected: false,
        };
    },
    computed: {
        selectAll() {
            return store.getters['selecting/selectAll'];
        },
        sorting() {
            return store.getters['general/getSorting'];
        },
    },
    watch: {
        selectAll() {
            this.selected = this.selectAll;
        },
        selected() {
            this.selected ? store.dispatch('selecting/select', this.id) : store.dispatch('selecting/deSelect', this.id);
        },
        sorting() {
            if (this.sorting) this.selected = false;
        },
    },
};
</script>
