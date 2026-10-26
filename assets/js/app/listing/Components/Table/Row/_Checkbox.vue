<template>
    <div v-show="!sorting" key="checkbox" class="listing--checkbox">
        <div class="form-check">
            <input :id="`row-${id}`" v-model="selected" class="form-check-input" type="checkbox" />
            <label class="form-check-label" :for="`row-${id}`" @click="selected != !selected"></label>
        </div>
    </div>
</template>

<script>
import { mapState } from 'pinia';
import { useGeneralStore, useSelectingStore } from '../../../store';

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
        ...mapState(useSelectingStore, ['selectAll']),
        ...mapState(useGeneralStore, ['sorting']),
    },
    watch: {
        selectAll() {
            this.selected = this.selectAll;
        },
        selected() {
            this.selected
                ? useSelectingStore(this.$pinia).select(this.id)
                : useSelectingStore(this.$pinia).deSelect(this.id);
        },
        sorting() {
            if (this.sorting) this.selected = false;
        },
    },
};
</script>
