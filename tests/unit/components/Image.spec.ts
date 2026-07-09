import { mount, flushPromises, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import ImageField from '@/editor/Components/Image.vue';
import Axios from 'axios';
import { renable } from '@/patience-is-a-virtue';
import { resetModalContent } from '@/modal';
import { Modal } from 'bootstrap';
import noScroll from 'no-scroll';
import baguetteBox from 'baguettebox.js';
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

vi.mock('axios', () => ({
    default: { get: vi.fn(), post: vi.fn() },
}));
vi.mock('@/patience-is-a-virtue', () => ({ renable: vi.fn() }));
vi.mock('@/modal', () => ({ resetModalContent: vi.fn() }));
vi.mock('no-scroll', () => ({ default: { on: vi.fn(), off: vi.fn() } }));
vi.mock('baguettebox.js', () => ({ default: { run: vi.fn() } }));
vi.mock('bootstrap', () => {
    const modalInstance = { hide: vi.fn() };
    return {
        Modal: {
            getOrCreateInstance: vi.fn(() => modalInstance),
        },
    };
});

describe('EditorImage Component', () => {
    let wrapper: VueWrapper<ComponentPublicInstance>;
    type FieldEvent = [{ fieldName: string }];
    type UploadProgressConfig = {
        onUploadProgress: (event: { loaded: number; total: number }) => void;
    };

    const defaultProps = {
        filename: 'photo.jpg',
        name: 'fields[image]',
        id: 'field-image',
        required: false,
        readonly: false,
        thumbnail: '/thumbs/400×300/photo.jpg',
        alt: 'A photo',
        includeAlt: true,
        directory: '/async/upload',
        directoryurl: '/async/upload/url',
        media: 7,
        csrfToken: 'csrf123',
        labels: {
            image_preview: 'Preview',
            placeholder_filename: 'Filename…',
            placeholder_alt_text: 'Alt text…',
            button_upload: 'Upload',
            button_upload_options: 'Options',
            modal_title_images: 'Select an image',
            modal_title_upload_from_url: 'Upload from URL',
            button_from_library: 'From library',
            button_from_url: 'From URL',
            button_edit_attributes: 'Edit attributes',
            button_move_up: 'Move up',
            button_move_down: 'Move down',
            button_remove: 'Remove',
        },
        filelist: '/async/files?location=files',
        extensions: ['jpg', 'png', 'xyz'],
        attributesLink: '/attributes',
        inImagelist: false,
        isFirstInImagelist: false,
        isLastInImagelist: false,
        errormessage: 'Error!',
        pattern: '.+',
        placeholder: false,
        extraFields: {},
        extraData: {},
    };

    const serverFiles = [
        {
            group: 'files',
            value: 'files/photo.jpg',
            text: 'photo.jpg',
            base_url_path: '/async/browse',
        },
        { group: 'directories', value: 'files/subdir/', text: 'subdir' },
        { group: 'files', value: 'files/weird.xyz', text: 'weird.xyz' },
        { group: 'files', value: 'files/virus.exe', text: 'virus.exe' },
    ];

    const filenameInput = () => wrapper.find('input[name="fields[image][filename]"]');
    const altInput = () => wrapper.find('input[name="fields[image][alt]"]');
    const uploadInput = () => wrapper.find('input[type="file"]');

    beforeEach(() => {
        document.body.innerHTML = `
            <div id="resourcesModal">
                <div class="modal-dialog">
                    <div class="modal-title"></div>
                    <div class="modal-body"></div>
                    <div class="modal-footer"></div>
                </div>
            </div>
            <button id="modalButtonAccept"></button>
        `;
    });

    afterEach(() => {
        wrapper.unmount();
        document.body.innerHTML = '';
        vi.clearAllMocks();
        vi.restoreAllMocks();
        vi.useRealTimers();
    });

    it('renders the filename, media, alt and upload inputs', () => {
        wrapper = mount(ImageField, { props: defaultProps });

        expect((wrapper.find('input[name="fields[image][media]"]').element as HTMLInputElement).value).toBe('7');
        expect((filenameInput().element as HTMLInputElement).value).toBe('photo.jpg');
        expect(filenameInput().attributes('data-errormessage')).toBe('Error!');
        expect((altInput().element as HTMLInputElement).value).toBe('A photo');
        expect(altInput().attributes('placeholder')).toBe('Alt text…');
        expect(altInput().attributes('pattern')).toBe('.+');
        expect(uploadInput().attributes('name')).toBe('fields[image][]');
        expect(uploadInput().attributes('id')).toBe('field-image');
        expect(uploadInput().attributes('accept')).toBe('.jpg,.png,.xyz');
        expect(wrapper.find('a.dropdown-item').attributes('href')).toBe('/attributes?file=photo.jpg');
    });

    it('derives the preview and thumbnail from the filename', async () => {
        wrapper = mount(ImageField, { props: defaultProps });
        await wrapper.vm.$nextTick();

        const preview = wrapper.find('.editor__image--preview-image');
        expect(preview.attributes('href')).toBe('/thumbs/1000×1000/photo.jpg');
        expect(preview.attributes('style')).toContain('/thumbs/400×300/photo.jpg');
    });

    it('hides the preview when there is no filename', () => {
        wrapper = mount(ImageField, {
            props: { ...defaultProps, filename: '' },
        });
        expect(wrapper.find('.editor__image--preview-image').exists()).toBe(false);

        wrapper.unmount();
        wrapper = mount(ImageField, {
            props: { ...defaultProps, filename: undefined },
        });
        expect(wrapper.find('.editor__image--preview-image').exists()).toBe(false);
    });

    it('omits the alt input when includeAlt is off', () => {
        wrapper = mount(ImageField, {
            props: { ...defaultProps, includeAlt: false },
        });
        expect(altInput().exists()).toBe(false);
    });

    it('omits optional attributes when they are unset', () => {
        wrapper = mount(ImageField, {
            props: {
                ...defaultProps,
                errormessage: false,
                pattern: false,
                labels: { ...defaultProps.labels, placeholder_alt_text: '' },
            },
        });

        expect(filenameInput().attributes('data-errormessage')).toBeUndefined();
        expect(altInput().attributes('pattern')).toBeUndefined();
        expect(altInput().attributes('placeholder')).toBeUndefined();
    });

    it('uses the placeholder prop for the alt input when set', () => {
        wrapper = mount(ImageField, {
            props: { ...defaultProps, placeholder: 'Custom alt…' },
        });
        expect(altInput().attributes('placeholder')).toBe('Custom alt…');
    });

    it('renders extra fields bound to the extra data', async () => {
        wrapper = mount(ImageField, {
            props: {
                ...defaultProps,
                extraFields: {
                    caption: { label: 'Caption', placeholder: 'Caption…' },
                },
                extraData: { caption: 'Old caption' },
            },
        });

        const captionInput = wrapper.find('input[name="fields[image][caption]"]');
        expect((captionInput.element as HTMLInputElement).value).toBe('Old caption');
        expect(captionInput.attributes('placeholder')).toBe('Caption…');

        await captionInput.setValue('New caption');
        expect((captionInput.element as HTMLInputElement).value).toBe('New caption');
    });

    it('accepts missing extra data', () => {
        wrapper = mount(ImageField, {
            props: { ...defaultProps, extraData: undefined },
        });

        expect(wrapper.find('.editor__image').exists()).toBe(true);
    });

    it('binds a declared extra field when passed a whole image record', () => {
        // In an imagelist the parent passes the entire ImageField record as
        // extra-data (base keys like filename/media alongside the extra values).
        // The declared extra field must still bind to its stored value.
        wrapper = mount(ImageField, {
            props: {
                ...defaultProps,
                extraFields: {
                    caption: { label: 'Caption', placeholder: 'Caption…' },
                },
                extraData: {
                    filename: 'foo.jpg',
                    media: 5,
                    thumbnail: '/thumbs/foo.jpg',
                    extra: [],
                    caption: 'Old caption',
                },
            },
        });

        const captionInput = wrapper.find('input[name="fields[image][caption]"]');
        expect((captionInput.element as HTMLInputElement).value).toBe('Old caption');
    });

    it('accepts extra data passed as an array', () => {
        wrapper = mount(ImageField, {
            props: {
                ...defaultProps,
                extraFields: { 0: { label: 'First', placeholder: '' } },
                extraData: ['first'],
            },
        });

        expect((wrapper.find('input[name="fields[image][0]"]').element as HTMLInputElement).value).toBe('first');
    });

    it('keeps the edited alt text in the input', async () => {
        wrapper = mount(ImageField, { props: defaultProps });

        await altInput().setValue('New alt');

        expect((altInput().element as HTMLInputElement).value).toBe('New alt');
    });

    it('toggles page scrolling around the lightbox', async () => {
        wrapper = mount(ImageField, { props: defaultProps });
        await wrapper.vm.$nextTick();

        const options = vi.mocked(baguetteBox.run).mock.calls.at(-1)?.[1];
        expect(options).toBeDefined();
        options?.afterShow?.();
        expect(noScroll.on).toHaveBeenCalled();
        options?.afterHide?.();
        expect(noScroll.off).toHaveBeenCalled();
    });

    it('does not reinitialize the lightbox for unrelated field edits', async () => {
        wrapper = mount(ImageField, { props: defaultProps });
        await wrapper.vm.$nextTick();
        vi.mocked(baguetteBox.run).mockClear();

        await altInput().setValue('New alt');
        await wrapper.vm.$nextTick();

        expect(baguetteBox.run).not.toHaveBeenCalled();
    });

    it('emits move events with the field name when in an imagelist', async () => {
        wrapper = mount(ImageField, {
            props: { ...defaultProps, inImagelist: true },
        });

        const [moveUp, moveDown] = wrapper
            .findAll('button')
            .filter(b => b.text().includes('Move up') || b.text().includes('Move down'));

        expect(moveUp).toBeDefined();
        expect(moveDown).toBeDefined();
        await moveUp?.trigger('click');
        await moveDown?.trigger('click');

        const moveImageUpEvents = wrapper.emitted('moveImageUp') as FieldEvent[];
        const moveImageDownEvents = wrapper.emitted('moveImageDown') as FieldEvent[];
        expect(moveImageUpEvents[0]?.[0].fieldName).toBe('fields[image][]');
        expect(moveImageDownEvents[0]?.[0].fieldName).toBe('fields[image][]');
    });

    it('clears the image and alt and emits remove when removing', async () => {
        wrapper = mount(ImageField, { props: defaultProps });

        const removeButton = wrapper.findAll('button').find(b => b.text().includes('Remove'));
        expect(removeButton).toBeDefined();
        await removeButton?.trigger('click');

        expect((filenameInput().element as HTMLInputElement).value).toBe('');
        expect((altInput().element as HTMLInputElement).value).toBe('');
        expect(wrapper.find('.editor__image--preview-image').exists()).toBe(false);
        const removeEvents = wrapper.emitted('remove') as FieldEvent[];
        expect(removeEvents[0]?.[0].fieldName).toBe('fields[image][]');
    });

    it('keeps the alt text when removing without includeAlt', async () => {
        wrapper = mount(ImageField, {
            props: { ...defaultProps, includeAlt: false },
        });

        const removeButton = wrapper.findAll('button').find(b => b.text().includes('Remove'));
        expect(removeButton).toBeDefined();
        await removeButton?.trigger('click');

        expect(wrapper.emitted('remove')).toHaveLength(1);
    });

    it('opens the file dialog when the upload button is clicked', async () => {
        wrapper = mount(ImageField, { props: defaultProps });
        const clickSpy = vi.spyOn(uploadInput().element as HTMLInputElement, 'click');

        const uploadButton = wrapper.findAll('button').find(b => b.text().includes('Upload'));
        expect(uploadButton).toBeDefined();
        await uploadButton?.trigger('click');

        expect(clickSpy).toHaveBeenCalled();
    });

    it('shows the drag overlay while dragging over the field', async () => {
        wrapper = mount(ImageField, { props: defaultProps });
        const overlayDisplay = () => (wrapper.find('.editor__image--dragging').element as HTMLElement).style.display;

        expect(overlayDisplay()).toBe('none');

        await wrapper.trigger('dragenter');
        await wrapper.trigger('dragenter');
        expect(overlayDisplay()).toBe('');

        await wrapper.trigger('dragleave');
        expect(overlayDisplay()).toBe('');

        await wrapper.trigger('dragleave');
        expect(overlayDisplay()).toBe('none');
    });

    it('uploads a dropped image and tracks the progress', async () => {
        let resolvePost: (value: { data: string }) => void = () => {};
        (Axios.post as import('vitest').Mock).mockImplementation(
            () =>
                new Promise(resolve => {
                    resolvePost = resolve;
                }),
        );

        wrapper = mount(ImageField, { props: defaultProps });
        const image = new File(['dummy'], 'new.jpg');

        await wrapper.trigger('drop', { dataTransfer: { files: [image] } });

        const [url, formData, config] = (Axios.post as import('vitest').Mock).mock.calls[0] as [
            string,
            FormData,
            UploadProgressConfig,
        ];
        expect(url).toBe('/async/upload');
        expect(formData.get('image')).toBe(image);
        expect(formData.get('_csrf_token')).toBe('csrf123');

        config.onUploadProgress({ loaded: 1, total: 2 });
        await wrapper.vm.$nextTick();
        expect(wrapper.find('.progress-bar').attributes('aria-valuenow')).toBe('50');

        resolvePost({ data: 'new.jpg' });
        await flushPromises();

        expect((filenameInput().element as HTMLInputElement).value).toBe('new.jpg');
        expect(wrapper.find('.editor__image--preview-image').attributes('href')).toBe('/thumbs/1000×1000/new.jpg');
        expect(wrapper.find('.progress').exists()).toBe(false);
    });

    it('uploads an image picked through the file input', async () => {
        (Axios.post as import('vitest').Mock).mockResolvedValue({ data: 'picked.jpg' });
        wrapper = mount(ImageField, { props: defaultProps });

        const image = new File(['dummy'], 'picked.jpg');
        Object.defineProperty(uploadInput().element, 'files', {
            value: [image],
        });
        await uploadInput().trigger('change');
        await flushPromises();

        expect((filenameInput().element as HTMLInputElement).value).toBe('picked.jpg');
    });

    it('alerts when the upload fails', async () => {
        const alertSpy = vi.spyOn(window, 'alert').mockImplementation(() => {});
        const warnSpy = vi.spyOn(console, 'warn').mockImplementation(() => {});
        (Axios.post as import('vitest').Mock).mockRejectedValue({
            response: { data: { error: { message: 'too big' } } },
        });

        wrapper = mount(ImageField, { props: defaultProps });
        await wrapper.trigger('drop', {
            dataTransfer: { files: [new File(['x'], 'x.jpg')] },
        });
        await flushPromises();

        expect(alertSpy).toHaveBeenCalledWith('too big');
        expect(warnSpy).toHaveBeenCalledWith('too big');
        expect(wrapper.find('.progress').exists()).toBe(false);
    });

    it('browses the server images in a modal and applies the selection', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({ data: serverFiles });
        wrapper = mount(ImageField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        expect(Axios.get as import('vitest').Mock).toHaveBeenCalledWith('/async/files?location=files');

        const modalBody = document.querySelector('#resourcesModal .modal-body') as HTMLElement;
        expect((document.querySelector('#resourcesModal .modal-title') as HTMLElement).innerHTML).toBe(
            'Select an image',
        );
        // Image cards use thumbnails, directories render as folders, exe is filtered
        expect(modalBody.innerHTML).toContain('/thumbs/250×150×crop/photo.jpg');
        expect(modalBody.innerHTML).toContain('fa-folder');
        expect(modalBody.innerHTML).not.toContain('virus.exe');
        expect(document.querySelector('#resourcesModal .modal-footer')).toBeNull();
        expect(renable).toHaveBeenCalled();

        const checkbox = modalBody.querySelector('input.form-check-input') as HTMLInputElement;
        checkbox.click();
        expect(
            Modal.getOrCreateInstance(document.getElementById('resourcesModal') as HTMLElement).hide,
        ).toHaveBeenCalled();

        checkbox.checked = true;
        document.getElementById('resourcesModal')?.dispatchEvent(new Event('hidden.bs.modal'));
        await wrapper.vm.$nextTick();

        expect((filenameInput().element as HTMLInputElement).value).toBe('photo.jpg');
        expect(resetModalContent).toHaveBeenCalledWith(defaultProps.labels);
    });

    it('keeps the filename when the modal closes without a selection', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({ data: serverFiles });
        wrapper = mount(ImageField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        document.getElementById('resourcesModal')?.dispatchEvent(new Event('hidden.bs.modal'));

        expect((filenameInput().element as HTMLInputElement).value).toBe('photo.jpg');
    });

    it('adds a back arrow when browsing inside a subdirectory', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({
            data: [
                {
                    group: 'files',
                    value: 'files/subdir/photo.jpg',
                    text: 'photo.jpg',
                    base_url_path: '/async/browse',
                },
            ],
        });
        wrapper = mount(ImageField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        expect((document.querySelector('#resourcesModal .modal-body') as HTMLElement).innerHTML).toContain(
            'fa-level-up-alt',
        );
    });

    it('shows no back arrow at the top level', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({
            data: [
                {
                    group: 'files',
                    value: 'photo.jpg',
                    text: 'photo.jpg',
                    base_url_path: '/async/browse',
                },
            ],
        });
        wrapper = mount(ImageField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        expect((document.querySelector('#resourcesModal .modal-body') as HTMLElement).innerHTML).not.toContain(
            'fa-level-up-alt',
        );
    });

    it('navigates into a directory from the modal', async () => {
        (Axios.get as import('vitest').Mock).mockResolvedValue({ data: serverFiles });
        wrapper = mount(ImageField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        const directoryLink = document.querySelector('#resourcesModal .directory') as HTMLAnchorElement;
        directoryLink.click();
        await flushPromises();

        expect(Axios.get as import('vitest').Mock).toHaveBeenCalledTimes(2);
        expect((document.querySelector('#resourcesModal .modal-title') as HTMLElement).innerHTML).toContain(
            'Select an image:',
        );

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

    it('alerts when loading the server images fails', async () => {
        const alertSpy = vi.spyOn(window, 'alert').mockImplementation(() => {});
        (Axios.get as import('vitest').Mock).mockRejectedValue({
            response: { data: 'no access' },
        });

        wrapper = mount(ImageField, { props: defaultProps });
        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        expect(alertSpy).toHaveBeenCalledWith('no access<br>Image did not upload.');
        expect(renable).toHaveBeenCalled();
    });

    it('surfaces the server message when the failure is a real AxiosError', async () => {
        const alertSpy = vi.spyOn(window, 'alert').mockImplementation(() => {});
        // A genuine AxiosError is an `Error` instance that also carries `.response`.
        // The generic `error.message` must not shadow the server-provided message.
        const axiosError = Object.assign(new Error('Request failed with status code 400'), {
            response: { data: { error: { message: 'Server says no' } } },
        });
        (Axios.get as import('vitest').Mock).mockRejectedValue(axiosError);

        wrapper = mount(ImageField, { props: defaultProps });
        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        expect(alertSpy).toHaveBeenCalledWith('Server says no<br>Image did not upload.');
    });

    it('alerts when navigating a directory fails', async () => {
        const alertSpy = vi.spyOn(window, 'alert').mockImplementation(() => {});
        (Axios.get as import('vitest').Mock).mockResolvedValueOnce({ data: serverFiles });
        wrapper = mount(ImageField, { props: defaultProps });

        await wrapper.find('button.dropdown-item').trigger('click');
        await flushPromises();

        (Axios.get as import('vitest').Mock).mockRejectedValueOnce({
            response: { data: 'no access' },
        });
        (document.querySelector('#resourcesModal .directory') as HTMLElement).click();
        await flushPromises();

        expect(alertSpy).toHaveBeenCalledWith('no access<br>Image did not upload.');
        expect(renable).toHaveBeenCalled();
    });

    it('uploads an image from a url through the modal', async () => {
        vi.useFakeTimers();
        (Axios.post as import('vitest').Mock).mockResolvedValue({ data: 'remote.jpg' });
        wrapper = mount(ImageField, { props: defaultProps });

        const fromUrlButton = wrapper.findAll('button').find(b => b.text().includes('From URL'));
        expect(fromUrlButton).toBeDefined();
        await fromUrlButton?.trigger('click');

        expect((document.querySelector('#resourcesModal .modal-title') as HTMLElement).innerHTML).toBe(
            'Upload from URL',
        );
        expect(renable).toHaveBeenCalled();

        vi.advanceTimersByTime(5);
        const urlInput = document.querySelector('input[name=from-url-input]') as HTMLInputElement;
        expect(urlInput).not.toBeNull();

        urlInput.value = 'https://example.org/remote.jpg';
        document.getElementById('modalButtonAccept')?.click();
        await flushPromises();

        const [url, formData, config] = (Axios.post as import('vitest').Mock).mock.calls[0] as [
            string,
            FormData,
            UploadProgressConfig,
        ];
        expect(url).toBe('/async/upload/url');
        expect(formData.get('url')).toBe('https://example.org/remote.jpg');
        expect(formData.get('_csrf_token')).toBe('csrf123');
        expect((filenameInput().element as HTMLInputElement).value).toBe('remote.jpg');

        // The url upload reports progress through the same handler
        config.onUploadProgress({ loaded: 3, total: 4 });
        await wrapper.vm.$nextTick();
        expect(wrapper.find('.progress-bar').attributes('aria-valuenow')).toBe('75');

        document.getElementById('resourcesModal')?.dispatchEvent(new Event('hidden.bs.modal'));
        expect(resetModalContent).toHaveBeenCalledWith(defaultProps.labels);
    });

    it('does not upload from url when the input is empty', async () => {
        vi.useFakeTimers();
        wrapper = mount(ImageField, { props: defaultProps });

        const fromUrlButton = wrapper.findAll('button').find(b => b.text().includes('From URL'));
        expect(fromUrlButton).toBeDefined();
        await fromUrlButton?.trigger('click');
        vi.advanceTimersByTime(5);

        document.getElementById('modalButtonAccept')?.click();

        expect(Axios.post as import('vitest').Mock).not.toHaveBeenCalled();
    });

    it('alerts when the url upload fails', async () => {
        vi.useFakeTimers();
        const alertSpy = vi.spyOn(window, 'alert').mockImplementation(() => {});
        const warnSpy = vi.spyOn(console, 'warn').mockImplementation(() => {});
        (Axios.post as import('vitest').Mock).mockRejectedValue({
            response: { data: { error: { message: 'bad url' } } },
        });

        wrapper = mount(ImageField, { props: defaultProps });
        const fromUrlButton = wrapper.findAll('button').find(b => b.text().includes('From URL'));
        expect(fromUrlButton).toBeDefined();
        await fromUrlButton?.trigger('click');
        vi.advanceTimersByTime(5);

        (document.querySelector('input[name=from-url-input]') as HTMLInputElement).value =
            'https://example.org/bad.jpg';
        document.getElementById('modalButtonAccept')?.click();
        await flushPromises();

        expect(alertSpy).toHaveBeenCalledWith('bad url');
        expect(warnSpy).toHaveBeenCalledWith('bad url');
    });
});
