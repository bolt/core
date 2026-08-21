import { beforeEach, describe, expect, it, vi } from 'vitest';
import { flushPromises } from '../helpers/async';

const dispatchCtrlS = () => {
    document.dispatchEvent(
        new KeyboardEvent('keydown', { key: 's', code: 'KeyS', keyCode: 83, ctrlKey: true, bubbles: true }),
    );
};

const loadShortcut = async () => {
    vi.resetModules();
    await import('@/save-on-ctrl-s');
    await flushPromises();
};

beforeEach(() => {
    document.body.innerHTML = `
        <form id="editcontent">
            <button name="save" type="submit">Save</button>
        </form>
    `;
});

describe('save-on-ctrl-s', () => {
    it('saves the record when ctrl+s is pressed', async () => {
        await loadShortcut();
        const save = vi.fn();
        document.querySelector('button[name=save]')!.addEventListener('click', save);

        dispatchCtrlS();

        expect(save).toHaveBeenCalledTimes(1);
    });

    it('does nothing on a page with no edit form', async () => {
        document.body.innerHTML = '<div>Dashboard</div>';
        await loadShortcut();

        expect(() => dispatchCtrlS()).not.toThrow();
    });

    it('ignores the s key on its own', async () => {
        await loadShortcut();
        const save = vi.fn();
        document.querySelector('button[name=save]')!.addEventListener('click', save);

        document.dispatchEvent(new KeyboardEvent('keydown', { key: 's', code: 'KeyS', keyCode: 83, bubbles: true }));

        expect(save).not.toHaveBeenCalled();
    });
});
