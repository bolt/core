import { defineStore } from 'pinia';

interface GeneralState {
    slimSidebar: boolean;
}

export const useGeneralStore = defineStore('editor-general', {
    state: (): GeneralState => ({
        slimSidebar: false,
    }),
});
