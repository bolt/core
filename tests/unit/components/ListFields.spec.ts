import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import Filelist from '@/editor/Components/Filelist.vue';
import Imagelist from '@/editor/Components/Imagelist.vue';

const labels = {
    add_new_file: 'Add file',
    add_new_image: 'Add image',
};

describe('Editor list fields', () => {
    it('mounts filelist with default empty files', () => {
        const wrapper = mount(Filelist, {
            props: {
                name: 'fields[files]',
                labels,
                limit: 10,
            },
            global: {
                stubs: {
                    'editor-file': true,
                },
            },
        });

        expect(wrapper.vm.containerFiles).toEqual([]);
        expect(wrapper.find('input[type="hidden"]').attributes('name')).toBe('fields[files]');
    });

    it('mounts imagelist with default empty images', () => {
        const wrapper = mount(Imagelist, {
            props: {
                name: 'fields[images]',
                labels,
                limit: 10,
                extraFields: [],
            },
            global: {
                stubs: {
                    'editor-image': true,
                },
            },
        });

        expect(wrapper.vm.containerImages).toEqual([]);
        expect(wrapper.find('input[type="hidden"]').attributes('name')).toBe('fields[images]');
    });
});
