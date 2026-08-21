import { beforeEach, describe, expect, it, vi } from 'vitest';
import $ from 'jquery';
import { flushPromises } from '../helpers/async';

const { toastInstance } = vi.hoisted(() => ({ toastInstance: { show: vi.fn() } }));
// A plain function, not an arrow: ajax-save.js calls `new Toast(...)`.
vi.mock('bootstrap', () => ({
    Toast: vi.fn(function () {
        return toastInstance;
    }),
}));

/**
 * ajax-save.js is a script, not a module: it reads the edit form at import time
 * and wires everything up in a jQuery ready handler. So each test lays out the
 * DOM first, then imports a fresh copy.
 */

type AjaxOptions = {
    type: string;
    data: string;
    beforeSend: () => void;
    complete: () => void;
    success: (data: unknown, textStatus: string) => void;
    error: (jq: unknown, status: string, err: unknown) => void;
};

let ajax: ReturnType<typeof vi.fn>;

const editFormDom = (options: { recordId?: string; ajaxButton?: boolean; classicButton?: boolean } = {}) => {
    const { recordId = '42', ajaxButton = true, classicButton = false } = options;
    document.body.innerHTML = `
        <div class="admin__header--title-inner">Old title</div>
        <small class="admin__modified-at">Yesterday</small>
        <div class="admin__notifications"></div>
        <form id="editcontent" ${recordId === '' ? '' : `data-record="${recordId}"`}>
            <input name="title" value="A page" />
            ${ajaxButton ? '<button name="save" type="button"><i class="fas fa-save"></i></button>' : ''}
            ${classicButton ? '<button name="save" type="submit">Save</button>' : ''}
        </form>
    `;
};

const loadAjaxSave = async () => {
    vi.resetModules();
    await import('@/ajax-save');
    await flushPromises();
};

/** The options handed to $.ajax by the last save. */
const lastCall = (): AjaxOptions => ajax.mock.calls[ajax.mock.calls.length - 1][0] as AjaxOptions;

const saveButton = () => $('button[name="save"][type="button"]');

beforeEach(() => {
    toastInstance.show.mockClear();
    ajax = vi.fn();
    $.ajax = ajax as unknown as typeof $.ajax;
    window.onbeforeunload = null;
});

describe('ajax-save.js', () => {
    it('posts the serialised form when the ajax save button is used', async () => {
        editFormDom();
        await loadAjaxSave();

        saveButton().trigger('click');

        expect(ajax).toHaveBeenCalledTimes(1);
        expect(lastCall().type).toBe('POST');
        expect(lastCall().data).toBe('title=A%20page');
    });

    it('disables the save button for the duration of the request', async () => {
        editFormDom();
        await loadAjaxSave();

        saveButton().trigger('click');
        expect(saveButton().prop('disabled')).toBe(true);

        lastCall().complete();

        expect(saveButton().prop('disabled')).toBe(false);
    });

    it('spins the save button while the request is in flight', async () => {
        editFormDom();
        await loadAjaxSave();

        saveButton().trigger('click');
        lastCall().beforeSend();

        expect(saveButton().find('i').attr('class')).toContain('fa-spin');

        lastCall().complete();

        expect(saveButton().find('i').attr('class')).toBe('fas fa-save');
    });

    it('updates the title and modified date on a successful save', async () => {
        editFormDom();
        await loadAjaxSave();

        saveButton().trigger('click');
        lastCall().success(
            {
                type: 'Saved',
                message: 'Saved it',
                status: 'success',
                notification: 'Notification',
                title: 'New title',
                modified: 'Just now',
            },
            'success',
        );

        expect($('.admin__header--title-inner').html()).toBe('New title');
        expect($('.admin__modified-at').html()).toBe('Just now');
    });

    it('shows a toast carrying the server message', async () => {
        editFormDom();
        await loadAjaxSave();

        saveButton().trigger('click');
        lastCall().success(
            { type: 'Saved', message: 'Saved it', status: 'success', notification: 'Notification' },
            'success',
        );

        expect($('#toastBody').text()).toBe('Saved it');
        expect($('#toastNotification').text()).toBe('Notification');
        expect($('#toastType').text()).toBe('Saved');
        expect($('#toastTitle').attr('class')).toContain('bg-toast-success');
    });

    it('actually shows the toast it builds', async () => {
        const { Toast } = await import('bootstrap');
        editFormDom();
        await loadAjaxSave();

        saveButton().trigger('click');
        lastCall().success(
            { type: 'Saved', message: 'Saved it', status: 'success', notification: 'Notification' },
            'success',
        );
        // The toast is instantiated from a nested ready handler, a turn later.
        await flushPromises();

        expect(Toast).toHaveBeenCalledWith(document.querySelector('.toast'), []);
        expect(toastInstance.show).toHaveBeenCalled();
    });

    it('reports a save the server did not accept', async () => {
        // A 200 response whose payload says otherwise takes the generic toast
        // path, which throws — see the note on that test below.
        editFormDom();
        await loadAjaxSave();

        saveButton().trigger('click');

        expect(() => lastCall().success({ status: 'warning' }, 'notmodified')).toThrow(ReferenceError);
    });

    it('redirects to the new record after saving a new one', async () => {
        const location = { pathname: '/bolt/new/pages', replace: vi.fn() };
        vi.stubGlobal('location', location);
        editFormDom({ recordId: '' });
        await loadAjaxSave();

        saveButton().trigger('click');
        lastCall().success({ url: '/bolt/edit/99' }, 'success');

        expect(location.replace).toHaveBeenCalledWith('/bolt/edit/99');
    });

    it('redirects after duplicating a record', async () => {
        const location = { pathname: '/bolt/duplicate/42', replace: vi.fn() };
        vi.stubGlobal('location', location);
        editFormDom({ recordId: '42' });
        await loadAjaxSave();

        saveButton().trigger('click');
        lastCall().success({ url: '/bolt/edit/99' }, 'success');

        expect(location.replace).toHaveBeenCalledWith('/bolt/edit/99');
    });

    describe('when the save is rejected', () => {
        const validationFailure = {
            status: 422,
            responseJSON: {
                type: 'Error',
                status: 'warning',
                notification: 'Notification',
                errors: [
                    { property: 'title', message: 'This value should not be blank.' },
                    { message: 'Something else is wrong.' },
                ],
            },
        };

        it('lists the validator messages above the form', async () => {
            editFormDom();
            await loadAjaxSave();

            saveButton().trigger('click');
            lastCall().error(validationFailure, 'error', 'Unprocessable Entity');

            const alerts = $('#editcontent-validation-errors .alert')
                .toArray()
                .map(a => a.textContent);
            expect(alerts).toEqual(['title: This value should not be blank.', 'Something else is wrong.']);
        });

        it('summarises the messages in a toast', async () => {
            editFormDom();
            await loadAjaxSave();

            saveButton().trigger('click');
            lastCall().error(validationFailure, 'error', 'Unprocessable Entity');

            expect($('#toastBody').text()).toBe('This value should not be blank. Something else is wrong.');
        });

        it('replaces the previous errors rather than stacking them up', async () => {
            editFormDom();
            await loadAjaxSave();

            saveButton().trigger('click');
            lastCall().error(validationFailure, 'error', 'Unprocessable Entity');
            saveButton().trigger('click');
            lastCall().error(validationFailure, 'error', 'Unprocessable Entity');

            expect($('#editcontent-validation-errors')).toHaveLength(1);
            expect($('#editcontent-validation-errors .alert')).toHaveLength(2);
        });

        it('clears the errors once a save succeeds', async () => {
            editFormDom();
            await loadAjaxSave();

            saveButton().trigger('click');
            lastCall().error(validationFailure, 'error', 'Unprocessable Entity');
            saveButton().trigger('click');
            lastCall().success({ type: 'Saved', message: 'Saved it', status: 'success' }, 'success');

            expect($('#editcontent-validation-errors')).toHaveLength(0);
        });

        it('shows the user nothing at all for any other failure', async () => {
            // Pinning current behaviour, which is a bug. The generic toast is
            // raised by calling showToast() with no arguments, but its last
            // parameter is declared `dom_element = dom_element` — a default that
            // refers to the parameter itself, not the module-level markup. That
            // is a temporal dead zone reference, so the call throws before it
            // can render anything and a failed save is silent.
            const log = vi.spyOn(console, 'log').mockImplementation(() => undefined);
            editFormDom();
            await loadAjaxSave();

            saveButton().trigger('click');

            expect(() => lastCall().error({ status: 500, responseJSON: null }, 'error', 'Server Error')).toThrow(
                ReferenceError,
            );
            expect(log).toHaveBeenCalledWith('error', 'Server Error');
            expect($('#toastBody')).toHaveLength(0);
            log.mockRestore();
        });
    });

    describe('leaving the page', () => {
        it('warns about unsaved changes once a field is edited', async () => {
            editFormDom();
            await loadAjaxSave();

            $('#editcontent').trigger('change');

            expect(window.onbeforeunload!(new Event('beforeunload'))).toContain('unsaved changes');
        });

        it('stays quiet while nothing has been edited', async () => {
            editFormDom();
            await loadAjaxSave();

            expect(window.onbeforeunload!(new Event('beforeunload'))).toBeUndefined();
        });

        it('stops warning once the changes are saved', async () => {
            editFormDom();
            await loadAjaxSave();

            $('#editcontent').trigger('change');
            saveButton().trigger('click');

            expect(window.onbeforeunload!(new Event('beforeunload'))).toBeUndefined();
        });

        it('does not intercept unload without the ajax save button', async () => {
            editFormDom({ ajaxButton: false, classicButton: true });
            await loadAjaxSave();

            expect(window.onbeforeunload).toBeNull();
        });
    });

    describe('the classic form post', () => {
        it('disables the submit button once the browser accepts the form', async () => {
            editFormDom({ ajaxButton: false, classicButton: true });
            await loadAjaxSave();

            $('#editcontent').triggerHandler('submit');

            expect($('button[name="save"][type="submit"]').prop('disabled')).toBe(true);
        });

        it('leaves the button usable when the form opens in another tab', async () => {
            // A 'Preview' submit renders elsewhere and leaves this page intact,
            // so the save button has to stay clickable. See #3778.
            editFormDom({ ajaxButton: false, classicButton: true });
            $('#editcontent').attr('target', '_blank');
            await loadAjaxSave();

            $('#editcontent').triggerHandler('submit');

            expect($('button[name="save"][type="submit"]').prop('disabled')).toBe(false);
        });

        it('still disables the button for a same-tab target', async () => {
            editFormDom({ ajaxButton: false, classicButton: true });
            $('#editcontent').attr('target', '_self');
            await loadAjaxSave();

            $('#editcontent').triggerHandler('submit');

            expect($('button[name="save"][type="submit"]').prop('disabled')).toBe(true);
        });
    });

    it('posts to the current page, since the url option is misnamed', async () => {
        // Pinning current behaviour. The request is built with `link:` rather
        // than `url:`, which jQuery ignores — so the POST goes to whatever page
        // the browser is on. That happens to be the edit page, which is why it
        // works.
        editFormDom();
        await loadAjaxSave();

        saveButton().trigger('click');

        expect(lastCall()).not.toHaveProperty('url');
        expect(lastCall()).toHaveProperty('link');
    });
});
