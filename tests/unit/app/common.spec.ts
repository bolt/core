import { beforeEach, describe, expect, it, vi } from 'vitest';
import $ from 'jquery';
import { flushPromises } from '../helpers/async';

/**
 * common.js is a script, not a module: everything it does happens inside a
 * jQuery ready handler that runs on import. So each test lays out the DOM it
 * needs first, then imports a fresh copy of the module.
 */
const loadCommon = async () => {
    vi.resetModules();
    await import('@/common');
    // The ready handler runs on a microtask once the document is already loaded.
    await flushPromises();
};

const givenDom = (html: string) => {
    document.body.innerHTML = html;
};

beforeEach(() => {
    document.documentElement.className = '';
    // Every import registers another delegated handler on `document`, and the
    // module has no teardown. Left alone they stack up and a single click runs
    // the same handler once per import. (Nothing removes them in the browser
    // either — it just does not matter there, since the script loads once.)
    $(document).off('click');
});

describe('common.js', () => {
    it('marks the page as javascript-enabled', async () => {
        await loadCommon();

        expect(document.documentElement.classList.contains('js')).toBe(true);
    });

    it('publishes the asset version for cache busting', async () => {
        await loadCommon();

        expect((window as unknown as { assetsVersion: string }).assetsVersion).toBeTruthy();
    });

    describe('the collapsible sidebar', () => {
        const sidebarDom = `
            <div class="admin__sidebar d-none"></div>
            <button class="admin-sidebar-toggler"></button>
        `;

        it('starts collapsed', async () => {
            givenDom(sidebarDom);
            await loadCommon();

            expect($('.admin__sidebar').hasClass('admin__sidebar--is-collapsed')).toBe(true);
        });

        it('expands when the toggler is clicked', async () => {
            givenDom(sidebarDom);
            await loadCommon();

            $('.admin-sidebar-toggler').trigger('click');

            expect($('.admin__sidebar').hasClass('admin__sidebar--is-expanded')).toBe(true);
            expect($('.admin__sidebar').hasClass('admin__sidebar--is-collapsed')).toBe(false);
            expect($('.admin__sidebar').hasClass('d-none')).toBe(false);
            expect($('.admin-sidebar-toggler').hasClass('is-active')).toBe(true);
        });

        it('collapses again on a second click', async () => {
            givenDom(sidebarDom);
            await loadCommon();

            $('.admin-sidebar-toggler').trigger('click');
            $('.admin-sidebar-toggler').trigger('click');

            expect($('.admin__sidebar').hasClass('admin__sidebar--is-collapsed')).toBe(true);
            expect($('.admin__sidebar').hasClass('admin__sidebar--is-expanded')).toBe(false);
            expect($('.admin-sidebar-toggler').hasClass('is-active')).toBe(false);
        });
    });

    it('rewrites iso dates as relative times', async () => {
        const lastWeek = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString();
        givenDom(`<span class="datetime-relative">${lastWeek}</span>`);

        await loadCommon();

        expect($('.datetime-relative').text()).toBe('7 days ago');
    });

    it('initialises every popover trigger', async () => {
        givenDom('<button data-bs-toggle="popover" data-bs-content="Hi">Info</button>');

        await loadCommon();

        const { Popover } = await import('bootstrap');
        expect(Popover.getInstance(document.querySelector('[data-bs-toggle="popover"]')!)).not.toBeNull();
    });

    describe('read-only fields', () => {
        it('blocks typing into a field marked read-only', async () => {
            givenDom('<input data-readonly="readonly" />');
            await loadCommon();

            const event = $.Event('keydown');
            $('[data-readonly]').trigger(event);

            expect(event.isDefaultPrevented()).toBe(true);
        });

        it('blocks pasting into a field marked read-only', async () => {
            givenDom('<input data-readonly="readonly" />');
            await loadCommon();

            const event = $.Event('paste');
            $('[data-readonly]').trigger(event);

            expect(event.isDefaultPrevented()).toBe(true);
        });

        it('leaves the flatpickr alt input writable so validation still works', async () => {
            givenDom('<div><input class="editor--date" /><input class="alt" /></div>');

            await loadCommon();

            expect($('.alt').prop('readonly')).toBe(false);
            expect($('.alt').attr('data-readonly')).toBe('readonly');
        });
    });

    describe('custom validation messages', () => {
        it('shows the field message when the browser reports it invalid', async () => {
            givenDom('<input data-errormessage="Please pick a title" />');
            await loadCommon();

            const input = document.querySelector('input') as HTMLInputElement;
            const setCustomValidity = vi.spyOn(input, 'setCustomValidity');
            $(input).trigger('invalid');

            expect(setCustomValidity).toHaveBeenCalledWith('Please pick a title');
        });

        it('clears the message again as soon as the field is edited', async () => {
            givenDom('<input data-errormessage="Please pick a title" />');
            await loadCommon();

            const input = document.querySelector('input') as HTMLInputElement;
            const setCustomValidity = vi.spyOn(input, 'setCustomValidity');
            $(input).trigger('input');

            expect(setCustomValidity).toHaveBeenCalledWith('');
        });

        it('copies a date field message onto the input the user actually sees', async () => {
            givenDom('<div><input class="editor--date" data-errormessage="Pick a date" /><input class="alt" /></div>');

            await loadCommon();

            expect($('.alt').attr('data-errormessage')).toBe('Pick a date');
        });

        it('marks an emptied date field invalid', async () => {
            givenDom('<div><input class="editor--date" /><input class="alt" value="" /></div>');
            await loadCommon();

            const alt = document.querySelector('.alt') as HTMLInputElement;
            const setCustomValidity = vi.spyOn(alt, 'setCustomValidity');
            $('.editor--date').trigger('change');

            expect(setCustomValidity).toHaveBeenCalledWith('Please fill out this field.');
        });

        it('clears the message once the date field has a value', async () => {
            givenDom('<div><input class="editor--date" /><input class="alt" value="2024-03-15" /></div>');
            await loadCommon();

            const alt = document.querySelector('.alt') as HTMLInputElement;
            const setCustomValidity = vi.spyOn(alt, 'setCustomValidity');
            $('.editor--date').trigger('change');

            expect(setCustomValidity).toHaveBeenCalledWith('');
        });
    });

    describe('the shared modal', () => {
        const modalDom = `
            <a href="/bolt/delete/1" data-bs-toggle="modal" data-bs-target="#resourcesModal" data-modal-title="Are you sure?"
               data-modal-body="This cannot be undone.">Delete</a>
            <div id="resourcesModal" class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <h5 class="modal-title"></h5>
                        <div class="modal-body"></div>
                        <button id="modalButtonAccept"></button>
                    </div>
                </div>
            </div>
        `;

        it('fills the modal in from the element that opened it', async () => {
            givenDom(`
                <button data-bs-toggle="modal" data-bs-target="#resourcesModal" data-modal-title="Are you sure?"
                        data-modal-body="This cannot be undone.">Delete</button>
                <div id="resourcesModal" class="modal">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <h5 class="modal-title"></h5>
                            <div class="modal-body"></div>
                            <button id="modalButtonAccept"></button>
                        </div>
                    </div>
                </div>
            `);
            await loadCommon();

            $('[data-bs-toggle="modal"]').trigger('click');

            expect(document.querySelector('.modal-title')!.innerHTML).toBe('Are you sure?');
            expect(document.querySelector('.modal-body')!.innerHTML).toBe('This cannot be undone.');
        });

        it('drops the body when the modal is a confirmation for a link', async () => {
            givenDom(modalDom);
            await loadCommon();

            $('[data-bs-toggle="modal"]').trigger('click');

            expect(document.querySelector('.modal-body')).toBeNull();
        });

        it('follows the link when the confirmation is accepted', async () => {
            const location = { href: '' };
            vi.stubGlobal('location', location);
            givenDom(modalDom);
            await loadCommon();

            $('[data-bs-toggle="modal"]').trigger('click');
            document.getElementById('modalButtonAccept')!.click();

            expect(location.href).toBe('/bolt/delete/1');
        });

        it('puts the modal back to its loading state when it closes', async () => {
            givenDom(modalDom);
            await loadCommon();

            $('[data-bs-toggle="modal"]').trigger('click');
            document.getElementById('resourcesModal')!.dispatchEvent(new Event('hidden.bs.modal'));

            expect(document.querySelector('.modal-content')!.innerHTML).toContain('spinner-border');
            expect(document.querySelector('#modalButtonDeny')!.textContent).toBe('Close');
        });
    });

    it('opens the tab holding the first invalid field when saving', async () => {
        givenDom(`
            <div id="editor">
                <ul class="nav"><a href="#tab-meta">Meta</a></ul>
                <div class="tab-pane" id="tab-meta"><input required value="" /></div>
                <button type="submit">Save</button>
            </div>
        `);
        await loadCommon();

        const { Tab } = await import('bootstrap');
        const show = vi.spyOn(Tab, 'getOrCreateInstance');
        $('#editor button[type="submit"]').trigger('click');

        expect(show).toHaveBeenCalledWith(document.querySelector('.nav a[href="#tab-meta"]'));
    });

    describe('tabs and the url hash', () => {
        const tabDom = `
            <ul class="nav">
                <a href="#tab-meta" data-bs-toggle="tab">Meta</a>
                <a href="#tab-content" data-bs-toggle="tab">Content</a>
            </ul>
            <div class="tab-pane" id="tab-meta"></div>
            <div class="tab-pane" id="tab-content"></div>
        `;

        it('opens the tab named in the url hash on load', async () => {
            // Set the real hash rather than stubbing location: history.replaceState
            // rejects a url from another origin, and jQuery reads location.href.
            window.location.hash = '#tab-meta';
            givenDom(tabDom);
            const { Tab } = await import('bootstrap');
            const getOrCreate = vi.spyOn(Tab, 'getOrCreateInstance');

            await loadCommon();

            expect(getOrCreate).toHaveBeenCalledWith(document.querySelector('a[href="#tab-meta"]'));
            getOrCreate.mockRestore();
            window.location.hash = '';
        });

        it('rewrites the url without reloading when a tab is opened', async () => {
            givenDom(tabDom);
            await loadCommon();
            const replaceState = vi.spyOn(history, 'replaceState');

            $('a[href="#tab-content"]').trigger('click');

            expect(replaceState).toHaveBeenCalledWith(null, null, expect.stringContaining('#tab-content'));
            replaceState.mockRestore();
        });
    });
});
