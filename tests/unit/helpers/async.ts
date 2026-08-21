/**
 * @vue/test-utils v1 does not export flushPromises (v2 does). This matches the
 * v2 signature, so at the Vue 3 upgrade this file is deleted and the import
 * moves to '@vue/test-utils'.
 */
export function flushPromises(): Promise<void> {
    return new Promise(resolve => {
        setTimeout(resolve, 0);
    });
}
