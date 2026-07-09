import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import Filelist from '@/editor/Components/Filelist.vue';
import { describe, it, expect, beforeEach, afterEach } from 'vitest';

describe('EditorFilelist Component', () => {
    type FieldPayload = { fieldName: string };
    type FileItem = {
        id: number;
        filename?: string;
        directory?: string;
        name?: string;
        inFilelist?: boolean;
    };
    type FilelistExpose = {
        counter: number;
        containerFiles: FileItem[];
        allowMore: boolean;
        isFirstInFilelist: (index: number) => boolean;
        isLastInFilelist: (index: number) => boolean;
        getFieldNumberFromElement: (payload: FieldPayload) => number;
        onMoveFileDown: (payload: FieldPayload) => void;
        onMoveFileUp: (payload: FieldPayload) => void;
        onRemoveFile: (payload: FieldPayload) => void;
        fieldName: (index: number) => string;
        addFile: () => void;
    };
    let wrapper: VueWrapper<ComponentPublicInstance & FilelistExpose> | null = null;

    const defaultProps = {
        name: 'fields[gallery]',
        directory: '/foo',
        filelist: 'gallery',
        csrfToken: 'token123',
        labels: { add_new_file: 'Add new file' },
        extensions: ['jpg', 'png'],
        attributesLink: '/attributes',
        limit: 3,
        readonly: false,
        files: [
            {
                filename: '1.jpg',
                thumbnail: 'thumb1.jpg',
                title: 'One',
                media: '1',
            },
            {
                filename: '2.jpg',
                thumbnail: 'thumb2.jpg',
                title: 'Two',
                media: '2',
            },
        ],
    };

    beforeEach(() => {
        wrapper = mount(Filelist, {
            props: defaultProps,
            global: {
                stubs: {
                    'editor-file': true,
                },
            },
        });
    });

    afterEach(() => {
        if (wrapper) wrapper?.unmount();
        wrapper = null;
    });

    it('initializes correctly with files', () => {
        const files = wrapper!.findAll('editor-file-stub');
        expect(files).toHaveLength(2);
        expect(wrapper!.vm!.counter).toBe(2);

        expect(wrapper!.vm!.isFirstInFilelist(0)).toBe(true);
        expect(wrapper!.vm!.isFirstInFilelist(1)).toBe(false);
        expect(wrapper!.vm!.isLastInFilelist(0)).toBe(false);
        expect(wrapper!.vm!.isLastInFilelist(1)).toBe(true);
    });

    it('renders empty state hidden input when no files', async () => {
        wrapper = mount(Filelist, {
            props: { ...defaultProps, files: [] },
            global: { stubs: { 'editor-file': true } },
        });

        expect(wrapper!.vm!.containerFiles).toHaveLength(0);
        const hiddenInput = wrapper!.find('input[type="hidden"]');
        expect(hiddenInput.exists()).toBe(true);
        expect(hiddenInput.attributes('name')).toBe('fields[gallery]');
    });

    it('computes allowMore correctly', async () => {
        expect(wrapper!.vm!.allowMore).toBe(true);

        await wrapper!.setProps({ readonly: true });
        expect(wrapper!.vm!.allowMore).toBe(false);

        await wrapper!.setProps({ readonly: false });
        // Add a file to reach the limit
        wrapper!.vm!.addFile();
        expect(wrapper!.vm!.containerFiles).toHaveLength(3);
        expect(wrapper!.vm!.allowMore).toBe(false);
    });

    it('adds a file when button clicked', async () => {
        const button = wrapper!.find('button');
        await button.trigger('click');

        expect(wrapper!.vm!.containerFiles).toHaveLength(3);
        const addedFile = wrapper!.vm!.containerFiles[2];
        expect(addedFile.inFilelist).toBe(true);
        expect(addedFile.directory).toBe('/foo');
        expect(addedFile.name).toBe('fields[gallery]');
        expect(addedFile.id).toBe(2);
        expect(wrapper!.vm!.counter).toBe(3);
    });

    it('gets field number from element', () => {
        const num = wrapper!.vm!.getFieldNumberFromElement({
            fieldName: 'fields[gallery][1]',
        });
        expect(num).toBe(1);
    });

    it('moves file down', async () => {
        wrapper!.vm!.onMoveFileDown({ fieldName: 'fields[gallery][0]' });
        expect(wrapper!.vm!.containerFiles[0].filename).toBe('2.jpg');
        expect(wrapper!.vm!.containerFiles[1].filename).toBe('1.jpg');
    });

    it('does not move file down if it is the last', async () => {
        wrapper!.vm!.onMoveFileDown({ fieldName: 'fields[gallery][1]' });
        expect(wrapper!.vm!.containerFiles[0].filename).toBe('1.jpg');
        expect(wrapper!.vm!.containerFiles[1].filename).toBe('2.jpg');
    });

    it('moves file up', async () => {
        wrapper!.vm!.onMoveFileUp({ fieldName: 'fields[gallery][1]' });
        expect(wrapper!.vm!.containerFiles[0].filename).toBe('2.jpg');
        expect(wrapper!.vm!.containerFiles[1].filename).toBe('1.jpg');
    });

    it('does not move file up if it is the first', async () => {
        wrapper!.vm!.onMoveFileUp({ fieldName: 'fields[gallery][0]' });
        expect(wrapper!.vm!.containerFiles[0].filename).toBe('1.jpg');
        expect(wrapper!.vm!.containerFiles[1].filename).toBe('2.jpg');
    });

    it('removes a file', async () => {
        wrapper!.vm!.onRemoveFile({ fieldName: 'fields[gallery][0]' });
        expect(wrapper!.vm!.containerFiles).toHaveLength(1);
        expect(wrapper!.vm!.containerFiles[0].filename).toBe('2.jpg');
    });

    it('generates fieldName correctly', () => {
        expect(wrapper!.vm!.fieldName(5)).toBe('fields[gallery][5]');
    });

    it('handles undefined files', () => {
        const localWrapper = mount(Filelist, {
            props: { ...defaultProps, files: undefined },
            global: { stubs: { 'editor-file': true } },
        });
        expect(localWrapper.vm.containerFiles).toHaveLength(0);
    });

    it('computes allowMore without limit', () => {
        const localWrapper = mount(Filelist, {
            props: { ...defaultProps, limit: undefined },
            global: { stubs: { 'editor-file': true } },
        });
        expect(localWrapper.vm.allowMore).toBe(true);
        localWrapper.unmount();
    });
});
