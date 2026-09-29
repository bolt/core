import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ProgressBar from '@/editor/Components/ProgressBar.vue';

describe('ProgressBar', () => {
    it('renders the value and max as aria attributes', () => {
        const wrapper = mount(ProgressBar, { propsData: { value: 25, max: 50 } });

        const bar = wrapper.find('.progress-bar');
        expect(bar.attributes('aria-valuenow')).toBe('25');
        expect(bar.attributes('aria-valuemax')).toBe('50');
        expect(bar.attributes('aria-valuemin')).toBe('0');
    });

    it('sets the bar width to the value as a percentage of max', () => {
        const wrapper = mount(ProgressBar, { propsData: { value: 25, max: 50 } });

        expect((wrapper.find('.progress-bar').element as HTMLElement).style.width).toBe('50%');
    });

    it('defaults to an empty bar out of 100', () => {
        const wrapper = mount(ProgressBar);

        const bar = wrapper.find('.progress-bar');
        expect(bar.attributes('aria-valuenow')).toBe('0');
        expect(bar.attributes('aria-valuemax')).toBe('100');
        expect((bar.element as HTMLElement).style.width).toBe('0%');
    });

    it('applies an explicit height and omits it otherwise', () => {
        expect((mount(ProgressBar, { propsData: { height: '4px' } }).element as HTMLElement).style.height).toBe('4px');
        expect((mount(ProgressBar).element as HTMLElement).style.height).toBe('');
    });
});
