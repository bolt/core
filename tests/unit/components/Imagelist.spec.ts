import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import Imagelist from '@/editor/Components/Imagelist.vue';
import { describe, it, expect, beforeEach, afterEach } from 'vitest';

describe('EditorImagelist Component', () => {
    type FieldPayload = { fieldName: string };
    type ImageItem = {
        id: number;
        filename?: string;
        alt?: string;
        directory?: string;
        name?: string;
        inImagelist?: boolean;
        hidden?: boolean;
    };
    type ImagelistExpose = {
        counter: number;
        containerImages: ImageItem[];
        activeImageFields: ImageItem[];
        allowMore: boolean;
        isFirstInImagelist: (index: number) => boolean;
        isLastInImagelist: (index: number) => boolean;
        getActiveImageFields: () => ImageItem[];
        getFieldNumberFromElement: (payload: FieldPayload) => number;
        onMoveImageDown: (payload: FieldPayload) => void;
        onMoveImageUp: (payload: FieldPayload) => void;
        onRemoveImage: (payload: FieldPayload) => void;
        fieldName: (index: number) => string;
        addImage: () => void;
    };
    let wrapper: VueWrapper<ComponentPublicInstance & ImagelistExpose> | null = null;

    const defaultProps = {
        name: 'fields[gallery]',
        directory: '/foo',
        filelist: 'gallery',
        csrfToken: 'token123',
        labels: { add_new_image: 'Add new image' },
        extensions: ['jpg', 'png'],
        attributesLink: '/attributes',
        limit: 3,
        readonly: false,
        extraFields: [],
        images: [
            {
                filename: '1.jpg',
                thumbnail: 'thumb1.jpg',
                title: 'One',
                media: 1,
                alt: 'Alt 1',
            },
            {
                filename: '2.jpg',
                thumbnail: 'thumb2.jpg',
                title: 'Two',
                media: 2,
                alt: 'Alt 2',
            },
        ],
    };

    beforeEach(() => {
        wrapper = mount(Imagelist, {
            props: defaultProps,
            global: {
                stubs: {
                    'editor-image': true,
                },
            },
        });
    });

    afterEach(() => {
        if (wrapper) wrapper?.unmount();
        wrapper = null;
    });

    it('initializes correctly with images', () => {
        const images = wrapper!.findAll('editor-image-stub');
        expect(images).toHaveLength(2);
        expect(wrapper!.vm!.counter).toBe(2);

        expect(wrapper!.vm!.isFirstInImagelist(0)).toBe(true);
        expect(wrapper!.vm!.isFirstInImagelist(1)).toBe(false);
        expect(wrapper!.vm!.isLastInImagelist(0)).toBe(false);
        expect(wrapper!.vm!.isLastInImagelist(1)).toBe(true);
    });

    it('renders empty state hidden input when no images', async () => {
        wrapper = mount(Imagelist, {
            props: { ...defaultProps, images: [] },
            global: { stubs: { 'editor-image': true } },
        });

        expect(wrapper!.vm!.getActiveImageFields()).toHaveLength(0);
        const hiddenInput = wrapper!.find('input[type="hidden"]');
        expect(hiddenInput.exists()).toBe(true);
        expect(hiddenInput.attributes('name')).toBe('fields[gallery]');
    });

    it('computes allowMore correctly', async () => {
        expect(wrapper!.vm!.allowMore).toBe(true);

        await wrapper!.setProps({ readonly: true });
        expect(wrapper!.vm!.allowMore).toBe(false);

        await wrapper!.setProps({ readonly: false });
        // Add an image to reach the limit
        wrapper!.vm!.addImage();
        expect(wrapper!.vm!.getActiveImageFields()).toHaveLength(3);
        expect(wrapper!.vm!.allowMore).toBe(false);
    });

    it('adds an image when button clicked', async () => {
        const button = wrapper!.find('button');
        await button.trigger('click');

        expect(wrapper!.vm!.containerImages).toHaveLength(3);
        const addedImage = wrapper!.vm!.containerImages[2];
        expect(addedImage.inImagelist).toBe(true);
        expect(addedImage.directory).toBe('/foo');
        expect(addedImage.name).toBe('fields[gallery]');
        expect(addedImage.id).toBe(2);
        expect(addedImage.alt).toBe('');
        expect(wrapper!.vm!.counter).toBe(3);
    });

    it('gets field number from element', () => {
        const num = wrapper!.vm!.getFieldNumberFromElement({
            fieldName: 'fields[gallery][1]',
        });
        expect(num).toBe(1);
    });

    it('moves image down', async () => {
        wrapper!.vm!.onMoveImageDown({ fieldName: 'fields[gallery][0]' });
        expect(wrapper!.vm!.containerImages[0].filename).toBe('2.jpg');
        expect(wrapper!.vm!.containerImages[1].filename).toBe('1.jpg');
    });

    it('does not move image down if it is the last', async () => {
        wrapper!.vm!.onMoveImageDown({ fieldName: 'fields[gallery][1]' });
        expect(wrapper!.vm!.containerImages[0].filename).toBe('1.jpg');
        expect(wrapper!.vm!.containerImages[1].filename).toBe('2.jpg');
    });

    it('moves image up', async () => {
        wrapper!.vm!.onMoveImageUp({ fieldName: 'fields[gallery][1]' });
        expect(wrapper!.vm!.containerImages[0].filename).toBe('2.jpg');
        expect(wrapper!.vm!.containerImages[1].filename).toBe('1.jpg');
    });

    it('does not move image up if it is the first', async () => {
        wrapper!.vm!.onMoveImageUp({ fieldName: 'fields[gallery][0]' });
        expect(wrapper!.vm!.containerImages[0].filename).toBe('1.jpg');
        expect(wrapper!.vm!.containerImages[1].filename).toBe('2.jpg');
    });

    it('removes an image (soft delete)', async () => {
        wrapper!.vm!.onRemoveImage({ fieldName: 'fields[gallery][0]' });
        // The array length stays the same
        expect(wrapper!.vm!.containerImages).toHaveLength(2);
        // The item is hidden
        expect(wrapper!.vm!.containerImages[0].hidden).toBe(true);
        // Active images decreases
        expect(wrapper!.vm!.getActiveImageFields()).toHaveLength(1);
    });

    it('calculates first and last state from visible images after a soft delete', () => {
        wrapper!.vm!.onRemoveImage({ fieldName: 'fields[gallery][0]' });

        expect(wrapper!.vm!.activeImageFields).toHaveLength(1);
        expect(wrapper!.vm!.activeImageFields[0].filename).toBe('2.jpg');
        expect(wrapper!.vm!.isFirstInImagelist(0)).toBe(true);
        expect(wrapper!.vm!.isLastInImagelist(0)).toBe(true);
    });

    it('moves images between visible neighbors when hidden images are present', () => {
        wrapper?.unmount();
        wrapper = mount(Imagelist, {
            props: {
                ...defaultProps,
                images: [
                    ...defaultProps.images,
                    {
                        filename: '3.jpg',
                        thumbnail: 'thumb3.jpg',
                        title: 'Three',
                        media: 3,
                        alt: 'Alt 3',
                    },
                ],
            },
            global: { stubs: { 'editor-image': true } },
        });

        wrapper!.vm!.onRemoveImage({ fieldName: 'fields[gallery][0]' });
        wrapper!.vm!.onMoveImageDown({ fieldName: 'fields[gallery][1]' });

        expect(wrapper!.vm!.containerImages[1].filename).toBe('3.jpg');
        expect(wrapper!.vm!.containerImages[2].filename).toBe('2.jpg');

        wrapper!.vm!.onMoveImageUp({ fieldName: 'fields[gallery][2]' });

        expect(wrapper!.vm!.containerImages[1].filename).toBe('2.jpg');
        expect(wrapper!.vm!.containerImages[2].filename).toBe('3.jpg');
    });

    it('generates fieldName correctly', () => {
        expect(wrapper!.vm!.fieldName(5)).toBe('fields[gallery][5]');
    });

    it('handles undefined images', () => {
        const localWrapper = mount(Imagelist, {
            props: { ...defaultProps, images: undefined },
            global: { stubs: { 'editor-image': true } },
        });
        expect(localWrapper.vm.containerImages).toHaveLength(0);
    });

    it('computes allowMore without limit', () => {
        const localWrapper = mount(Imagelist, {
            props: { ...defaultProps, limit: undefined },
            global: { stubs: { 'editor-image': true } },
        });
        expect(localWrapper.vm.allowMore).toBe(true);
        localWrapper.unmount();
    });
});
