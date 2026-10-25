import { defineStore } from 'pinia';
import type { ListingRecord } from '../types';

interface GeneralState {
    type: string | null;
    rowSize: string;
    sorting: boolean;
}

export const useGeneralStore = defineStore('listing-general', {
    state: (): GeneralState => ({
        type: null,
        rowSize: 'normal',
        sorting: false,
    }),
});

interface ListingState {
    records: ListingRecord[];
}

export const useListingStore = defineStore('listing', {
    state: (): ListingState => ({
        records: [],
    }),
});

interface SelectingState {
    selectAll: boolean;
    selected: number[];
}

export const useSelectingStore = defineStore('selecting', {
    state: (): SelectingState => ({
        selectAll: false,
        selected: [],
    }),
    getters: {
        selectedCount: state => state.selected.length,
    },
    actions: {
        select(id: number) {
            if (!this.selected.includes(id)) {
                this.selected.push(id);
            }
        },
        deSelect(id: number) {
            const index = this.selected.indexOf(id);
            if (index > -1) this.selected.splice(index, 1);
        },
    },
});
