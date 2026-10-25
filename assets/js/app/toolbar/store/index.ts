import { defineStore } from 'pinia';

interface GeneralState {
    toolbarColor: string;
}

export const useGeneralStore = defineStore('toolbar-general', {
    state: (): GeneralState => ({
        toolbarColor: '',
    }),
});
