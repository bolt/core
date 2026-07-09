import { defineStore } from 'pinia';

interface GeneralState {
    slimSidebar: boolean;
}

export const useGeneralStore = defineStore('sidebar-general', {
    state: (): GeneralState => ({
        slimSidebar: false,
    }),
});
