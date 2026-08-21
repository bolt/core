import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import Axios from 'axios';
import { Modal } from 'bootstrap';
import FileField from '@/editor/Components/File.vue';
import { renable } from '@/patience-is-a-virtue';
import { resetModalContent } from '@/modal';
import { withResourcesModal } from '../helpers/dom';
import { flushPromises } from '../helpers/async';

vi.mock('axios', () => ({ default: { get: vi.fn(), post: vi.fn() } }));
vi.mock('@/patience-is-a-virtue', () => ({ renable: vi.fn() }));
vi.mock('@/modal', () => ({ resetModalContent: vi.fn() }));
vi.mock('bootstrap', () => ({
    Modal: { getOrCreateInstance: vi.fn(() => ({ hide: vi.fn() })) },
}));

const labels = {
    placeholder_filename: 'No file selected',
    placeholder_title: 'Describe the file',
    button_upload: 'Upload',
    button_upload_options: 'Upload options',
    button_from_library: 'From library',
    button_edit_attributes: 'Edit attributes',
    button_move_up: 'Move up',
    button_move_down: 'Move down',
    button_remove: 'Remove',
    modal_title_files: 'Select a file',
};

const defaultProps = {
    name: 'fields[document]',
    filename: 'report.pdf',
    title: 'The report',
    directory: '/bolt/upload/files',
    media: '',
    csrfToken: 'token-123',
    labels,
    filelist: '/bolt/async/filelist',
    extensions: ['pdf', 'txt'],
    inFilelist: false,
    isFirstInFilelist: false,
    isLastInFilelist: false,
    attributesLink: '/bolt/attributes',
    required: false,
    readonly: false,
};

const mountFile = (props: Record<string, unknown> = {}) =>
    mount(FileField, { propsData: { ...defaultProps, ...props } });

const value = (wrapper: ReturnType<typeof mount>, selector: string) =>
    (wrapper.find(selector).element as HTMLInputElement).value;
const filenameInput = 'input[name="fields[document][filename]"]';
const titleInput = 'input[name="fields[document][title]"]';
const button = (wrapper: ReturnType<typeof mount>, label: string) =>
    wrapper.findAll('button').wrappers.find(b => b.text().trim() === label)!;

const serverFiles = [
    { text: 'files/', group: 'directories', value: 'files/', base_url_path: '/bolt/async/filelist' },
    { text: 'a.pdf', group: 'files', value: 'files/a.pdf', base_url_path: '/bolt/async/filelist' },
    { text: 'skip.exe', group: 'files', value: 'files/skip.exe', base_url_path: '/bolt/async/filelist' },
];

afterEach(() => {
    vi.mocked(Axios.get).mockReset();
    vi.mocked(Axios.post).mockReset();
    vi.mocked(renable).mockReset();
    vi.mocked(resetModalContent).mockReset();
    vi.mocked(Modal.getOrCreateInstance).mockClear();
});

describe('EditorFile', () => {
    it('renders the filename and title with their field names', async () => {
        const wrapper = mountFile();
        await nextTick();

        expect(value(wrapper, filenameInput)).toBe('report.pdf');
        expect(value(wrapper, titleInput)).toBe('The report');
        expect(wrapper.find(filenameInput).attributes('title')).toBe('fields[document] filename');
        expect(wrapper.find(titleInput).attributes('title')).toBe('fields[document] title');
    });

    it('posts the media reference alongside the file', () => {
        const wrapper = mountFile({ media: 'media-42' });

        expect(value(wrapper, 'input[name="fields[document][media]"]')).toBe('media-42');
    });

    it('accepts only the configured extensions', () => {
        const wrapper = mountFile();

        expect(wrapper.find('input[type="file"]').attributes('accept')).toBe('.pdf,.txt');
        expect(wrapper.find('input[type="file"]').attributes('name')).toBe('fields[document][]');
    });

    it('falls back to the label placeholder for the title', () => {
        const wrapper = mountFile();

        expect(wrapper.find(titleInput).attributes('placeholder')).toBe('Describe the file');
    });

    it('prefers an explicit placeholder for the title', () => {
        const wrapper = mountFile({ placeholder: 'Custom placeholder' });

        expect(wrapper.find(titleInput).attributes('placeholder')).toBe('Custom placeholder');
    });

    it('offers the attributes link only once a file is chosen', async () => {
        const withFile = mountFile();
        await nextTick();
        expect(withFile.find('a.dropdown-item').attributes('href')).toBe('/bolt/attributes?file=report.pdf');

        const empty = mountFile({ filename: '' });
        await nextTick();
        expect(empty.find('a.dropdown-item').exists()).toBe(false);
    });

    it('hides the reordering buttons outside a file list', () => {
        const wrapper = mountFile({ inFilelist: false });

        expect(button(wrapper, 'Move up')).toBeUndefined();
        expect(button(wrapper, 'Move down')).toBeUndefined();
    });

    it('asks its list to move it up and down', async () => {
        const wrapper = mountFile({ inFilelist: true });

        await button(wrapper, 'Move up').trigger('click');
        await button(wrapper, 'Move down').trigger('click');

        expect(wrapper.emitted('move-file-up')).toHaveLength(1);
        expect(wrapper.emitted('move-file-down')).toHaveLength(1);
    });

    it('cannot move the first file up or the last one down', () => {
        const first = mountFile({ inFilelist: true, isFirstInFilelist: true });
        expect(button(first, 'Move up').attributes('disabled')).toBeDefined();
        expect(button(first, 'Move down').attributes('disabled')).toBeUndefined();

        const last = mountFile({ inFilelist: true, isLastInFilelist: true });
        expect(button(last, 'Move down').attributes('disabled')).toBeDefined();
    });

    it('clears itself and asks its list to remove it', async () => {
        const wrapper = mountFile();
        await nextTick();

        await button(wrapper, 'Remove').trigger('click');

        expect(value(wrapper, filenameInput)).toBe('');
        expect(value(wrapper, titleInput)).toBe('');
        expect(wrapper.emitted('remove')).toHaveLength(1);
    });

    it('disables every action when read-only', () => {
        const wrapper = mountFile({ readonly: true, inFilelist: true });

        expect(button(wrapper, 'Upload').attributes('disabled')).toBeDefined();
        expect(button(wrapper, 'Remove').attributes('disabled')).toBeDefined();
        expect(wrapper.find(titleInput).attributes('readonly')).toBeDefined();
    });

    it('opens the file dialog from the upload button', async () => {
        const wrapper = mountFile();
        const click = vi.spyOn(wrapper.find('input[type="file"]').element as HTMLInputElement, 'click');

        await button(wrapper, 'Upload').trigger('click');

        expect(click).toHaveBeenCalled();
    });

    describe('uploading', () => {
        const file = new File(['content'], 'new.pdf', { type: 'application/pdf' });

        it('posts the file to the upload directory with the csrf token', async () => {
            vi.mocked(Axios.post).mockResolvedValue({ data: 'new.pdf' });
            const wrapper = mountFile();
            await nextTick();

            await wrapper.find('.editor__file').trigger('drop', { dataTransfer: { files: [file] } });
            await flushPromises();
            await nextTick();

            const [url, body] = vi.mocked(Axios.post).mock.calls[0] as unknown as [string, FormData];
            expect(url).toBe('/bolt/upload/files');
            expect(body.get('file')).toBe(file);
            expect(body.get('_csrf_token')).toBe('token-123');
            expect(value(wrapper, filenameInput)).toBe('new.pdf');
        });

        it('shows upload progress and clears it when the upload finishes', async () => {
            // The upload has to stay in flight while progress is reported, so
            // hold the response back until the assertions are done.
            let reportProgress: ((event: { loaded: number; total: number }) => void) | undefined;
            let finishUpload!: (response: { data: string }) => void;
            vi.mocked(Axios.post).mockImplementation((_url, _body, config) => {
                reportProgress = (config as { onUploadProgress: typeof reportProgress }).onUploadProgress;
                return new Promise(resolve => {
                    finishUpload = resolve;
                }) as never;
            });
            const wrapper = mountFile();
            await nextTick();
            expect(wrapper.find('.progress').exists()).toBe(false);

            await wrapper.find('.editor__file').trigger('drop', { dataTransfer: { files: [file] } });
            reportProgress!({ loaded: 40, total: 100 });
            await nextTick();

            expect(wrapper.find('.progress-bar').attributes('aria-valuenow')).toBe('40');
            expect((wrapper.find('.progress-bar').element as HTMLElement).style.width).toBe('40%');

            finishUpload({ data: 'new.pdf' });
            await flushPromises();
            await nextTick();

            expect(wrapper.find('.progress').exists()).toBe(false);
            expect(value(wrapper, filenameInput)).toBe('new.pdf');
        });

        it('warns and keeps the old file when the upload fails', async () => {
            const alert = vi.spyOn(window, 'alert').mockImplementation(() => undefined);
            const warn = vi.spyOn(console, 'warn').mockImplementation(() => undefined);
            vi.mocked(Axios.post).mockRejectedValue({ response: { data: 'File too large' } });
            const wrapper = mountFile();
            await nextTick();

            await wrapper.find('.editor__file').trigger('drop', { dataTransfer: { files: [file] } });
            await flushPromises();
            await nextTick();

            expect(alert).toHaveBeenCalledWith('File too large<br>File did not upload.');
            expect(value(wrapper, filenameInput)).toBe('report.pdf');
            alert.mockRestore();
            warn.mockRestore();
        });

        it('surfaces a structured error message', async () => {
            const alert = vi.spyOn(window, 'alert').mockImplementation(() => undefined);
            const warn = vi.spyOn(console, 'warn').mockImplementation(() => undefined);
            vi.mocked(Axios.post).mockRejectedValue({
                response: { data: { error: { message: 'Unsupported type' } } },
            });
            const wrapper = mountFile();
            await nextTick();

            await wrapper.find('.editor__file').trigger('drop', { dataTransfer: { files: [file] } });
            await flushPromises();

            expect(alert).toHaveBeenCalledWith('Unsupported type<br>File did not upload.');
            alert.mockRestore();
            warn.mockRestore();
        });

        it('falls back to a generic message for an unrecognised error', async () => {
            const alert = vi.spyOn(window, 'alert').mockImplementation(() => undefined);
            const warn = vi.spyOn(console, 'warn').mockImplementation(() => undefined);
            vi.mocked(Axios.post).mockRejectedValue({ response: { data: {} } });
            const wrapper = mountFile();
            await nextTick();

            await wrapper.find('.editor__file').trigger('drop', { dataTransfer: { files: [file] } });
            await flushPromises();

            expect(alert).toHaveBeenCalledWith('unknown error<br>File did not upload.');
            alert.mockRestore();
            warn.mockRestore();
        });

        it('highlights the field while a file is dragged over it', async () => {
            const wrapper = mountFile();
            const dropZone = wrapper.find('.editor__file');

            await dropZone.trigger('dragenter');
            expect(wrapper.find('.editor__file--dragging').isVisible()).toBe(true);

            await dropZone.trigger('dragleave');
            expect(wrapper.find('.editor__file--dragging').isVisible()).toBe(false);
        });

        it('keeps the highlight while dragging across child elements', async () => {
            const wrapper = mountFile();
            const dropZone = wrapper.find('.editor__file');

            await dropZone.trigger('dragenter');
            await dropZone.trigger('dragenter');
            await dropZone.trigger('dragleave');

            expect(wrapper.find('.editor__file--dragging').isVisible()).toBe(true);
        });
    });

    describe('browsing the server', () => {
        const openBrowser = async (wrapper: ReturnType<typeof mount>) => {
            withResourcesModal();
            await button(wrapper, 'From library').trigger('click');
            await flushPromises();
        };

        it('lists the server files in the modal', async () => {
            vi.mocked(Axios.get).mockResolvedValue({ data: serverFiles });
            const wrapper = mountFile();
            await nextTick();

            await openBrowser(wrapper);

            expect(Axios.get).toHaveBeenCalledWith('/bolt/async/filelist');
            const body = document.querySelector('.modal-body')!;
            expect(body.innerHTML).toContain('a.pdf');
            expect(document.querySelector('.modal-title')!.innerHTML).toBe('Select a file');
        });

        it('leaves out files whose extension is not allowed', async () => {
            vi.mocked(Axios.get).mockResolvedValue({ data: serverFiles });
            const wrapper = mountFile();
            await nextTick();

            await openBrowser(wrapper);

            expect(document.querySelector('.modal-body')!.innerHTML).not.toContain('skip.exe');
        });

        it('re-enables the button that opened the modal', async () => {
            vi.mocked(Axios.get).mockResolvedValue({ data: serverFiles });
            const wrapper = mountFile();
            await nextTick();

            await openBrowser(wrapper);

            expect(renable).toHaveBeenCalled();
        });

        it('takes the chosen file when the modal closes', async () => {
            vi.mocked(Axios.get).mockResolvedValue({ data: serverFiles });
            const wrapper = mountFile();
            await nextTick();
            await openBrowser(wrapper);

            const checkbox = document.querySelector('.modal-body input[type=checkbox]') as HTMLInputElement;
            checkbox.checked = true;
            document.getElementById('resourcesModal')!.dispatchEvent(new Event('hidden.bs.modal'));
            await nextTick();

            expect(value(wrapper, filenameInput)).toBe('a.pdf');
            expect(resetModalContent).toHaveBeenCalledWith(labels);
        });

        it('renders a folder as a link rather than a selectable file', async () => {
            vi.mocked(Axios.get).mockResolvedValue({ data: serverFiles });
            const wrapper = mountFile();
            await nextTick();

            await openBrowser(wrapper);

            const folder = document.querySelector('.modal-body a.directory') as HTMLAnchorElement;
            expect(folder).not.toBeNull();
            expect(folder.href).toContain('location=files/');
            expect(folder.href).toContain('type=files');
        });

        it('offers a way back up out of a subdirectory', async () => {
            vi.mocked(Axios.get).mockResolvedValue({
                data: [
                    { text: 'b.pdf', group: 'files', value: 'files/sub/b.pdf', base_url_path: '/bolt/async/filelist' },
                ],
            });
            const wrapper = mountFile();
            await nextTick();

            await openBrowser(wrapper);

            const up = document.querySelector('.modal-body a.directory') as HTMLAnchorElement;
            expect(up.href).toContain('location=files');
            expect(document.querySelector('.modal-body')!.textContent).toContain('../files');
        });

        it('lists the chosen folder when a directory is opened', async () => {
            vi.mocked(Axios.get).mockResolvedValue({ data: serverFiles });
            const wrapper = mountFile();
            await nextTick();
            await openBrowser(wrapper);

            vi.mocked(Axios.get).mockResolvedValue({
                data: [
                    {
                        text: 'deep.pdf',
                        group: 'files',
                        value: 'files/sub/deep.pdf',
                        base_url_path: '/bolt/async/filelist',
                    },
                ],
            });
            (document.querySelector('.modal-body a.directory') as HTMLAnchorElement).click();
            await flushPromises();

            expect(document.querySelector('.modal-body')!.innerHTML).toContain('deep.pdf');
            expect(document.querySelector('.modal-title')!.innerHTML).toContain('files/sub');
        });

        it('recovers when a directory cannot be opened', async () => {
            const warn = vi.spyOn(console, 'warn').mockImplementation(() => undefined);
            vi.mocked(Axios.get).mockResolvedValue({ data: serverFiles });
            const wrapper = mountFile();
            await nextTick();
            await openBrowser(wrapper);

            vi.mocked(Axios.get).mockRejectedValue(new Error('offline'));
            (document.querySelector('.modal-body a.directory') as HTMLAnchorElement).click();
            await flushPromises();

            expect(warn).toHaveBeenCalled();
            warn.mockRestore();
        });

        it('recovers when the file list cannot be loaded', async () => {
            const warn = vi.spyOn(console, 'warn').mockImplementation(() => undefined);
            vi.mocked(Axios.get).mockRejectedValue(new Error('offline'));
            const wrapper = mountFile();
            await nextTick();

            await openBrowser(wrapper);

            expect(warn).toHaveBeenCalled();
            expect(renable).toHaveBeenCalled();
            warn.mockRestore();
        });
    });

    it.skip('omits optional attributes when they are unset or false', () => {
        // Only passes after 2f840c0d, which declares the props Twig passes.
        // Until then `placeholder`, `pattern` and friends arrive as undeclared
        // attributes and fall through onto the root element instead of being
        // bound where the template expects them.
    });
});
