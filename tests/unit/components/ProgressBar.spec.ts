import { mount } from '@vue/test-utils';
import ProgressBar from '@/editor/Components/ProgressBar.vue';
import { describe, it, expect } from 'vitest';

describe('ProgressBar Component', () => {
    it('renders the progress relative to the max', () => {
        const wrapper = mount(ProgressBar, { props: { value: 1, max: 4 } });

        const bar = wrapper.find('.progress-bar');
        expect(bar.attributes('aria-valuenow')).toBe('1');
        expect(bar.attributes('aria-valuemax')).toBe('4');
        expect((bar.element as HTMLElement).style.width).toBe('25%');
    });

    it('defaults to an empty bar out of 100', () => {
        const wrapper = mount(ProgressBar);

        const bar = wrapper.find('.progress-bar');
        expect(bar.attributes('aria-valuenow')).toBe('0');
        expect(bar.attributes('aria-valuemax')).toBe('100');
        expect((bar.element as HTMLElement).style.width).toBe('0%');
    });

    it('applies the height when given', () => {
        const wrapper = mount(ProgressBar, { props: { height: '4px' } });
        expect((wrapper.find('.progress').element as HTMLElement).style.height).toBe('4px');
    });

    it('applies no height by default', () => {
        const wrapper = mount(ProgressBar);
        expect((wrapper.find('.progress').element as HTMLElement).style.height).toBe('');
    });
});
