import { flushPromises } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import Axios from 'axios';
import { Modal } from 'bootstrap';
import { createServerFileBrowser, type ServerFile } from '@/editor/utils/serverFileBrowser';
import { renable } from '@/patience-is-a-virtue';
import { resetModalContent } from '@/modal';
import { withResourcesModal } from '../helpers/dom';

vi.mock('axios', () => ({ default: { get: vi.fn() } }));
vi.mock('@/patience-is-a-virtue', () => ({ renable: vi.fn() }));
vi.mock('@/modal', () => ({ resetModalContent: vi.fn() }));
const { hide } = vi.hoisted(() => ({ hide: vi.fn() }));
vi.mock('bootstrap', () => ({ Modal: { getOrCreateInstance: vi.fn(() => ({ hide })) } }));

/**
 * The server file browser shared by File.vue and Image.vue. The component specs
 * drive it through the components; this covers the edges they do not reach.
 */

const labels = { modal_button_deny: 'Cancel', modal_button_save: 'Save' };

const files = [
    { group: 'directories', value: 'files/sub/', text: 'sub', base_url_path: '/bolt/async/filelist' },
    { group: 'files', value: 'files/report.pdf', text: 'report.pdf' },
    { group: 'files', value: 'files/notes.txt', text: 'notes.txt' },
    { group: 'files', value: 'files/README', text: 'README' },
];

// Renders each entry as the components do: a directory link, or a checkbox
// carrying the file's value.
const generateModalContent = vi.fn((options: ServerFile[]) =>
    options
        .map(option =>
            option.group === 'directories'
                ? `<a class="directory" href="/bolt/async/filelist?location=${option.value}">${option.text}</a>`
                : `<input class="form-check-input" type="checkbox" value="${option.value}">`,
        )
        .join(''),
);

const createBrowser = (initialFilelist = '/bolt/async/filelist') => {
    const onSelect = vi.fn();
    const onOpenError = vi.fn();
    const onNavigateError = vi.fn();
    const browser = createServerFileBrowser({
        initialFilelist,
        extensions: ['pdf'],
        labels,
        modalTitlePrefix: 'Select a file',
        generateModalContent,
        onSelect,
        onOpenError,
        onNavigateError,
    });
    return { ...browser, onSelect, onOpenError, onNavigateError };
};

const openerButton = (attributes: Record<string, string> = {}) => {
    const button = document.createElement('button');
    button.setAttribute('data-modal-title', 'Select a file');
    for (const [name, value] of Object.entries(attributes)) {
        button.setAttribute(name, value);
    }
    document.body.appendChild(button);
    return button;
};

const open = async (browser: ReturnType<typeof createBrowser>, button = openerButton()) => {
    const event = new MouseEvent('click');
    Object.defineProperty(event, 'currentTarget', { value: button });
    await browser.selectServerFile(event);
    await flushPromises();
};

beforeEach(() => {
    vi.mocked(Axios.get).mockReset();
    vi.mocked(renable).mockClear();
    vi.mocked(resetModalContent).mockClear();
    vi.mocked(Modal.getOrCreateInstance).mockClear();
    hide.mockClear();
    generateModalContent.mockClear();
});

describe('serverFileBrowser', () => {
    it('keeps directories and files with an allowed extension', () => {
        const { filterServerFiles } = createBrowser();

        expect(filterServerFiles(files).map(file => file.text)).toEqual(['sub', 'report.pdf']);
    });

    it('reports a missing file list url as an opening error', async () => {
        const browser = createBrowser('');

        await expect(browser.selectServerFile(new MouseEvent('click'))).rejects.toThrow('Missing server file list URL');
        expect(Axios.get).not.toHaveBeenCalled();
    });

    it('fills the modal and applies the dialog class the button asks for', async () => {
        vi.mocked(Axios.get).mockResolvedValue({ data: files });
        withResourcesModal();
        const browser = createBrowser();

        await open(browser, openerButton({ 'data-modal-dialog-class': 'modal-xl' }));

        expect(document.querySelector('.modal-dialog')!.classList.contains('modal-xl')).toBe(true);
        expect(document.querySelector('.modal-title')!.innerHTML).toBe('Select a file');
        expect(document.querySelector('.modal-footer')).toBeNull();
        expect(renable).toHaveBeenCalled();
    });

    it('closes the modal once a file is ticked, and hands the file over', async () => {
        vi.mocked(Axios.get).mockResolvedValue({ data: files });
        withResourcesModal();
        const browser = createBrowser();
        await open(browser);

        // Clicking ticks it.
        document.querySelector<HTMLInputElement>('.form-check-input')!.click();
        document.getElementById('resourcesModal')!.dispatchEvent(new Event('hidden.bs.modal'));

        expect(hide).toHaveBeenCalled();
        expect(browser.onSelect).toHaveBeenCalledWith('report.pdf');
        expect(resetModalContent).toHaveBeenCalledWith(labels);
    });

    it('hands nothing over when the modal is closed without a choice', async () => {
        vi.mocked(Axios.get).mockResolvedValue({ data: files });
        withResourcesModal();
        const browser = createBrowser();
        await open(browser);

        document.getElementById('resourcesModal')!.dispatchEvent(new Event('hidden.bs.modal'));

        expect(browser.onSelect).not.toHaveBeenCalled();
        expect(resetModalContent).toHaveBeenCalledWith(labels);
    });

    it('lists a folder in the open modal when a directory is clicked', async () => {
        vi.mocked(Axios.get)
            .mockResolvedValueOnce({ data: files })
            .mockResolvedValueOnce({ data: [{ group: 'files', value: 'files/sub/deep.pdf', text: 'deep.pdf' }] });
        withResourcesModal();
        const browser = createBrowser();
        await open(browser);

        document.querySelector<HTMLAnchorElement>('.directory')!.click();
        await flushPromises();

        expect(vi.mocked(Axios.get).mock.calls[1][0]).toContain('location=files/sub/');
        expect(document.querySelector('.modal-title')!.innerHTML).toContain('Select a file: ');
        expect(document.querySelector('.modal-title')!.innerHTML).toContain('files/sub');
    });

    it('leaves the modal as it is when a folder has nothing to list', async () => {
        vi.mocked(Axios.get).mockResolvedValue({ data: [] });
        withResourcesModal();
        document.querySelector('.modal-title')!.innerHTML = 'Unchanged';
        const browser = createBrowser();

        await browser.navigateDirectory();
        await flushPromises();

        expect(document.querySelector('.modal-title')!.innerHTML).toBe('Unchanged');
        expect(generateModalContent).not.toHaveBeenCalled();
    });

    it('reports a failed folder as a navigation error', async () => {
        vi.mocked(Axios.get).mockRejectedValue({ response: { data: 'No such folder' } });
        const browser = createBrowser();

        await browser.navigateDirectory();
        await flushPromises();

        expect(browser.onNavigateError.mock.calls[0][0].message).toBe('No such folder');
        expect(browser.onOpenError).not.toHaveBeenCalled();
        expect(renable).toHaveBeenCalled();
    });

    it('falls back to the error message when the server sent none', async () => {
        vi.mocked(Axios.get).mockRejectedValue(new Error('Network Error'));
        const browser = createBrowser();

        await open(browser);

        expect(browser.onOpenError.mock.calls[0][0].message).toBe('Network Error');
    });

    it('reports a modal missing from the page as an opening error', async () => {
        vi.mocked(Axios.get).mockResolvedValue({ data: files });
        const browser = createBrowser();

        await open(browser);

        expect(browser.onOpenError.mock.calls[0][0].message).toBe('Missing required element: #resourcesModal');
    });
});
