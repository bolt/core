import { beforeAll, beforeEach, describe, expect, it, vi } from 'vitest';

/**
 * hotkeys-js binds its keydown listener to `document` when it is first
 * imported, and has no teardown. So the script is imported once for the whole
 * file: importing a fresh copy per test would stack up another listener each
 * time, and a single keypress would click the save button once per import.
 *
 * The handler looks the form up when the key is pressed, not at import time,
 * so each test is free to lay out its own DOM.
 */

const dispatchCtrlS = () => {
    document.dispatchEvent(
        new KeyboardEvent('keydown', { key: 's', code: 'KeyS', keyCode: 83, ctrlKey: true, bubbles: true }),
    );
};

beforeAll(async () => {
    await import('@/save-on-ctrl-s');
});

beforeEach(() => {
    document.body.innerHTML = `
        <form id="editcontent">
            <button name="save" type="submit">Save</button>
        </form>
    `;
});

describe('save-on-ctrl-s', () => {
    it('saves the record when ctrl+s is pressed', () => {
        const save = vi.fn();
        document.querySelector('button[name=save]')!.addEventListener('click', save);

        dispatchCtrlS();

        expect(save).toHaveBeenCalledTimes(1);
    });

    it('does nothing on a page with no edit form', () => {
        // A save button outside the edit form, as other admin pages have.
        document.body.innerHTML = '<form id="other"><button name="save" type="submit">Save</button></form>';
        const save = vi.fn();
        document.querySelector('button[name=save]')!.addEventListener('click', save);

        dispatchCtrlS();

        expect(save).not.toHaveBeenCalled();
    });

    it('ignores the s key on its own', () => {
        const save = vi.fn();
        document.querySelector('button[name=save]')!.addEventListener('click', save);

        document.dispatchEvent(new KeyboardEvent('keydown', { key: 's', code: 'KeyS', keyCode: 83, bubbles: true }));

        expect(save).not.toHaveBeenCalled();
    });
});
