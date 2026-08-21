import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises } from '../helpers/async';

const { toastInstances } = vi.hoisted(() => ({ toastInstances: [] as { show: ReturnType<typeof vi.fn> }[] }));

// A plain function, not an arrow: notifications/index.js calls `new Toast(...)`.
vi.mock('bootstrap', () => ({
    Toast: vi.fn(function () {
        const instance = { show: vi.fn() };
        toastInstances.push(instance);
        return instance;
    }),
}));

/**
 * Shows the flash messages Twig renders into the page. Like the other scripts
 * under assets/js/app, this is a script rather than a module: it does its work
 * in a jQuery ready handler on import, so each test lays out the DOM first and
 * then imports a fresh copy.
 */
const loadNotifications = async () => {
    vi.resetModules();
    await import('@/notifications');
    await flushPromises();
};

beforeEach(() => {
    // vi.resetModules() gives the script a fresh copy, but the mocked Toast is
    // shared across those copies and keeps its call history.
    vi.clearAllMocks();
    toastInstances.length = 0;
});

describe('notifications', () => {
    it('shows every toast the page rendered', async () => {
        document.body.innerHTML = `
            <div class="toast">Saved</div>
            <div class="toast">Deleted</div>
        `;

        await loadNotifications();

        const { Toast } = await import('bootstrap');
        expect(Toast).toHaveBeenCalledTimes(2);
        expect(toastInstances).toHaveLength(2);
        toastInstances.forEach(instance => expect(instance.show).toHaveBeenCalledTimes(1));
    });

    it('builds each toast from its own element', async () => {
        document.body.innerHTML = '<div class="toast" id="first">Saved</div>';

        await loadNotifications();

        const { Toast } = await import('bootstrap');
        expect(Toast).toHaveBeenCalledWith(document.getElementById('first'), []);
    });

    it('does nothing on a page with no notifications', async () => {
        document.body.innerHTML = '<div>Dashboard</div>';

        await loadNotifications();

        const { Toast } = await import('bootstrap');
        expect(Toast).not.toHaveBeenCalled();
    });
});
