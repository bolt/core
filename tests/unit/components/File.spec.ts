import { mount, flushPromises, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import FileField from '@/editor/Components/File.vue';
import Axios from 'axios';
import { renable } from '@/patience-is-a-virtue';
import { resetModalContent } from '@/modal';
import { Modal } from 'bootstrap';
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

vi.mock('axios', () => ({
    default: { get: vi.fn(), post: vi.fn() },
}));
vi.mock('@/patience-is-a-virtue', () => ({ renable: vi.fn() }));
vi.mock('@/modal', () => ({ resetModalContent: vi.fn() }));
vi.mock('bootstrap', () => {
    const modalInstance = { hide: vi.fn() };
    return {
        Modal: {
            getOrCreateInstance: vi.fn(() => modalInstance),
        },
    };
});

describe('EditorFile Component', () => {
    let wrapper: VueWrapper<ComponentPublicInstance>;
    type FieldEvent = [{ fieldName: string }];
    type UploadProgressConfig = {
        onUploadProgress: (event: { loaded: number; total: number }) => void;
    };

    const defaultProps = {
        name: 'fields[file]',
        filename: 'documents/report.pdf',
        title: 'A report',
        directory: '/async/upload',
        media: '42',
        csrfToken: 'csrf123',
        labels: {
            placeholder_filename: 'Filename…',
            placeholder_title: 'Title…',
            button_upload: 'Upload',
            button_upload_options: 'Options',
            modal_title_files: 'Select a file',
            button_from_library: 'From library',
            button_edit_attributes: 'Edit attributes',
            button_move_up: 'Move up',
            button_move_down: 'Move down',
            button_remove: 'Remove',
        },
        filelist: '/async/files?location=files',
        extensions: ['pdf', 'txt', 'xyz'],
        inFilelist: false,
        isFirstInFilelist: false,
        isLastInFilelist: false,
        attributesLink: '/attributes',
        required: false,
        readonly: false,
        errormessage: 'Error!',
        pattern: '.+',
        placeholder: false,
    };

    const modalFixture = () => {
        document.body.innerHTML = `
            <div id="resourcesModal">
                <div class="modal-dialog">
                    <div class="modal-title"></div>
                    <div class="modal-body"></div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        `;
    };

    const serverFiles = [
        {
            group: 'files',
            value: 'files/report.pdf',
            text: 'report.pdf',
            base_url_path: '/async/browse',
        },
        { group: 'directories', value: 'files/subdir/', text: 'subdir' },
        { group: 'files', value: 'files/weird.xyz', text: 'weird.xyz' },
        { group: 'files', value: 'files/virus.exe', text: 'virus.exe' },
    ];

    const filenameInput = () => wrapper.find('input[name="fields[file][filename]"]');
    const titleInput = () => wrapper.find('input[name="fields[file][title]"]');
    const uploadInput = () => wrapper.find('input[type="file"]');

    beforeEach(() => {
        modalFixture();
    });

    afterEach(() => {
        wrapper.unmount();
        document.body.innerHTML = '';
        vi.clearAllMocks();
        vi.restoreAllMocks();
    });

    it('renders the filename, title, media and upload inputs', () => {
        wrapper = mount(FileField, { props: defaultProps });

        expect((wrapper.find('input[name="fields[file][media]"]').element as HTMLInputElement).value).toBe('42');
        expect((filenameInput().element as HTMLInputElement).value).toBe('documents/report.pdf');
        expect(filenameInput().attributes('data-errormessage')).toBe('Error!');
        expect((titleInput().element as HTMLInputElement).value).toBe('A report');
        expect(titleInput().attributes('placeholder')).toBe('Title…');
        expect(titleInput().attributes('pattern')).toBe('.+');
        expect(uploadInput().attributes('name')).toBe('fields[file][]');
        expect(uploadInput().attributes('accept')).toBe('.pdf,.txt,.xyz');
        expect(wrapper.find('a.dropdown-item').attributes('href')).toBe('/attributes?file=documents/report.pdf');
        expect(wrapper.find('.progress').exists()).toBe(false);
    });

    it('keeps the edited title in the input', async () => {
        wrapper = mount(FileField, { props: defaultProps });

        await titleInput().setValue('Renamed report');

        expect((titleInput().element as HTMLInputElement).value).toBe('Renamed report');
    });

    it('uses the placeholder prop for the title input when set', () => {
        wrapper = mount(FileField, {
            props: { ...defaultProps, placeholder: 'Custom title…' },
        });
        expect(titleInput().attributes('placeholder')).toBe('Custom title…');
    });

    it('omits optional attributes when they are unset', () => {
        wrapper = mount(FileField, {
            props: {
                ...defaultProps,
                errormessage: false,
                pattern: false,
                labels: { ...defaultProps.labels, placeholder_title: '' },
            },
        });

        expect(filenameInput().attributes('data-errormessage')).toBeUndefined();
        expect(titleInput().attributes('pattern')).toBeUndefined();
        expect(titleInput().attributes('placeholder')).toBeUndefined();
    });

    it('hides the attributes link when there is no filename', () => {
        wrapper = mount(FileField, {
            props: { ...defaultProps, filename: '' },
        });
        expect(wrapper.find('a.dropdown-item').exists()).toBe(false);
    });

    it('hides the move buttons outside a filelist', () => {
        wrapper = mount(FileField, { props: defaultProps });
        expect(wrapper.text()).not.toContain('Move up');
        expect(wrapper.text()).not.toContain('Move down');
    });

    it('emits move events with the field name when in a filelist', async () => {
        wrapper = mount(FileField, {
            props: { ...defaultProps, inFilelist: true },
        });

        const [moveUp, moveDown] = wrapper
            .findAll('button')
            .filter(b => b.text().includes('Move up') || b.text().includes('Move down'));

        expect(moveUp).toBeDefined();
        expect(moveDown).toBeDefined();
        await moveUp?.trigger('click');
        await moveDown?.trigger('click');

        const moveFileUpEvents = wrapper.emitted('moveFileUp') as FieldEvent[];
        const moveFileDownEvents = wrapper.emitted('moveFileDown') as FieldEvent[];
        expect(moveFileUpEvents).toHaveLength(1);
        expect(moveFileUpEvents[0]?.[0].fieldName).toBe('fields[file][]');
        expect(moveFileDownEvents).toHaveLength(1);
        expect(moveFileDownEvents[0]?.[0].fieldName).toBe('fields[file][]');
    });

    it('disables the move buttons at the list edges', () => {
        wrapper = mount(FileField, {
            props: {
                ...defaultProps,
                inFilelist: true,
                isFirstInFilelist: true,
                isLastInFilelist: true,
            },
        });

        const buttons = wrapper
            .findAll('button')
            .filter(b => b.text().includes('Move up') || b.text().includes('Move down'));
        expect(buttons[0]?.attributes('disabled')).toBeDefined();
        expect(buttons[1]?.attributes('disabled')).toBeDefined();
    });

    it('clears the inputs and emits remove when removing', async () => {
        wrapper = mount(FileField, { props: defaultProps });

        const removeButton = wrapper.findAll('button').find(b => b.text().includes('Remove'));
        expect(removeButton).toBeDefined();
        await removeButton?.trigger('click');

        expect((filenameInput().element as HTMLInputElement).value).toBe('');
        expect((titleInput().element as HTMLInputElement).value).toBe('');
        const removeEvents = wrapper.emitted('remove') as FieldEvent[];
        expect(removeEvents).toHaveLength(1);
        expect(removeEvents[0]?.[0].fieldName).toBe('fields[file][]');
    });

    it('opens the file dialog when the upload button is clicked', async () => {
        wrapper = mount(FileField, { props: defaultProps });
        const clickSpy = vi.spyOn(uploadInput().element as HTMLInputElement, 'click');

        const uploadButton = wrapper.findAll('button').find(b => b.text().includes('Upload'));
        expect(uploadButton).toBeDefined();
        await uploadButton?.trigger('click');

        expect(clickSpy).toHaveBeenCalled();
    });

    it('shows the drag overlay while dragging over the field', async () => {
        wrapper = mount(FileField, { props: defaultProps });
        const overlayDisplay = () => (wrapper.find('.editor__file--dragging').element as HTMLElement).style.display;

        expect(overlayDisplay()).toBe('none');

        await wrapper.trigger('dragenter');
        await wrapper.trigger('dragenter');
        expect(overlayDisplay()).toBe('');

        await wrapper.trigger('dragleave');
        expect(overlayDisplay()).toBe('');

        await wrapper.trigger('dragleave');
        expect(overlayDisplay()).toBe('none');
    });

    it('uploads a dropped file and tracks the progress', async () => {
        let resolvePost: (value: { data: string }) => void = () => {};
        (Axios.post as import('vitest').Mock).mockImplementation(
            () =>
                new Promise(resolve => {
                    resolvePost = resolve;
                }),
        );

        wrapper = mount(FileField, { props: defaultProps });
        const file = new File(['dummy'], 'new.pdf');

        await wrapper.trigger('drop', { dataTransfer: { files: [file] } });

        expect(Axios.post as import('vitest').Mock).toHaveBeenCalledTimes(1);
        const [url, formData, config] = (Axios.post as import('vitest').Mock).mock.calls[0] as [
            string,
            FormData,
            UploadProgressConfig,
        ];
        expect(url).toBe('/async/upload');
        expect(formData.get('file')).toBe(file);
        expect(formData.get('_csrf_token')).toBe('csrf123');

        // Simulate upload progress: the progress bar appears
        config.onUploadProgress({ loaded: 1, total: 2 });
        await wrapper.vm.$nextTick();
        expect(wrapper.find('.progress').exists()).toBe(true);
        expect(wrapper.find('.progress-bar').attributes('aria-valuenow')).toBe('50');

        resolvePost({ data: 'files/new.pdf' });
        await flushPromises();

        expect((filenameInput().element as HTMLInputElement).value).toBe('files/new.pdf');
        expect(wrapper.find('.progress').exists()).toBe(false);
    });

    it('uploads a file picked through the file input', async () => {
        (Axios.post as import('vitest').Mock).mockResolvedValue({ data: 'files/picked.pdf' });
        wrapper = mount(FileField, { props: defaultProps });

        const file = new File(['dummy'], 'picked.pdf');
        Object.defineProperty(uploadInput().element, 'files', {
            value: [file],
        });
        await uploadInput().trigger('change');
        await flushPromises();

        expect((filenameInput().element as HTMLInputElement).value).toBe('files/picked.pdf');
    });

    it.each([
        ['a string response', 'disk full', 'disk full'],
        ['an error object response', { error: { message: 'too big' } }, 'too big'],
        ['an unrecognized response', {}, 'upload error'],
    ])('alerts the upload failure with %s', async (_label, data, message) => {
        const alertSpy = vi.spyOn(window, 'alert').mockImplementation(() => {});
        const warnSpy = vi.spyOn(console, 'warn').mockImplementation(() => {});
        (Axios.post as import('vitest').Mock).mockRejectedValue({ response: { data } });

        wrapper = mount(FileField, { props: defaultProps });
        await wrapper.trigger('drop', {
            dataTransfer: { files: [new File(['x'], 'x.pdf')] },
        });
        await flushPromises();

        expect(alertSpy).toHaveBeenCalledWith(message + '<br>File did not upload.');
        expect(warnSpy).toHaveBeenCalled();
        expect(wrapper.find('.progress').exists()).toBe(false);
    });

    it('browses the server files in a modal and applies the selection', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({ data: serverFiles });
        wrapper = mount(FileField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        expect(Axios.get as import('vitest').Mock).toHaveBeenCalledWith('/async/files?location=files');

        const modalBody = document.querySelector('#resourcesModal .modal-body') as HTMLElement;
        const modalTitle = document.querySelector('#resourcesModal .modal-title') as HTMLElement;
        expect(modalTitle.innerHTML).toBe('Select a file');
        // Known extension gets its icon, unrecognized extension falls back, exe is filtered out
        expect(modalBody.innerHTML).toContain('fa-file-pdf');
        expect(modalBody.innerHTML).toContain('fa-file fa-5x');
        expect(modalBody.innerHTML).not.toContain('virus.exe');
        // Directories render as navigation cards
        expect(modalBody.innerHTML).toContain('fa-folder');
        // The footer is removed
        expect(document.querySelector('#resourcesModal .modal-footer')).toBeNull();
        expect(renable).toHaveBeenCalled();

        // Selecting a file hides the modal and applies the value on close
        const checkbox = modalBody.querySelector('input.form-check-input') as HTMLInputElement;
        checkbox.click();
        expect(
            Modal.getOrCreateInstance(document.getElementById('resourcesModal') as HTMLElement).hide,
        ).toHaveBeenCalled();

        checkbox.checked = true;
        document.getElementById('resourcesModal')?.dispatchEvent(new Event('hidden.bs.modal'));
        await wrapper.vm.$nextTick();

        expect((filenameInput().element as HTMLInputElement).value).toBe('report.pdf');
        expect(resetModalContent).toHaveBeenCalledWith(defaultProps.labels);
    });

    it('keeps the filename when the modal closes without a selection', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({ data: serverFiles });
        wrapper = mount(FileField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        document.getElementById('resourcesModal')?.dispatchEvent(new Event('hidden.bs.modal'));

        expect((filenameInput().element as HTMLInputElement).value).toBe('documents/report.pdf');
    });

    it('adds a back arrow when browsing inside a subdirectory', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({
            data: [
                {
                    group: 'files',
                    value: 'files/subdir/report.pdf',
                    text: 'report.pdf',
                    base_url_path: '/async/browse',
                },
            ],
        });
        wrapper = mount(FileField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        const modalBody = document.querySelector('#resourcesModal .modal-body') as HTMLElement;
        expect(modalBody.innerHTML).toContain('fa-level-up-alt');
        expect(modalBody.innerHTML).toContain('../files');
    });

    it('shows no back arrow at the top level', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({
            data: [
                {
                    group: 'files',
                    value: 'report.pdf',
                    text: 'report.pdf',
                    base_url_path: '/async/browse',
                },
            ],
        });
        wrapper = mount(FileField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        expect((document.querySelector('#resourcesModal .modal-body') as HTMLElement).innerHTML).not.toContain(
            'fa-level-up-alt',
        );
    });

    it('navigates into a directory from the modal', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({ data: serverFiles });
        wrapper = mount(FileField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        const directoryLink = document.querySelector('#resourcesModal .directory') as HTMLAnchorElement;
        directoryLink.click();
        await flushPromises();

        expect(Axios.get as import('vitest').Mock).toHaveBeenCalledTimes(2);
        expect((Axios.get as import('vitest').Mock).mock.calls[1][0]).toBe(directoryLink.href);
        expect((document.querySelector('#resourcesModal .modal-title') as HTMLElement).innerHTML).toContain(
            'Select a file:',
        );

        // Cards in the refreshed modal still close it on selection
        const checkbox = document.querySelector('#resourcesModal .form-check-input') as HTMLInputElement;
        checkbox.click();
        expect(
            Modal.getOrCreateInstance(document.getElementById('resourcesModal') as HTMLElement).hide,
        ).toHaveBeenCalled();

        // Directory links in the refreshed modal keep navigating deeper
        (document.querySelector('#resourcesModal .directory') as HTMLElement).click();
        await flushPromises();
        expect(Axios.get as import('vitest').Mock).toHaveBeenCalledTimes(3);
    });

    it('warns when loading the server files fails', async () => {
        const warnSpy = vi.spyOn(console, 'warn').mockImplementation(() => {});
        (Axios.get as import('vitest').Mock).mockRejectedValue(new Error('boom'));

        wrapper = mount(FileField, { props: defaultProps });
        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        expect(warnSpy).toHaveBeenCalled();
        expect(renable).toHaveBeenCalled();
    });

    it('warns when navigating a directory fails', async () => {
        const warnSpy = vi.spyOn(console, 'warn').mockImplementation(() => {});
        (Axios.get as import('vitest').Mock).mockResolvedValueOnce({ data: serverFiles });
        wrapper = mount(FileField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        (Axios.get as import('vitest').Mock).mockRejectedValueOnce(new Error('boom'));
        (document.querySelector('#resourcesModal .directory') as HTMLElement).click();
        await flushPromises();

        expect(warnSpy).toHaveBeenCalled();
        expect(renable).toHaveBeenCalled();
    });
});
