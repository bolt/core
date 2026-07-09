import { mount, type VueWrapper } from '@vue/test-utils';
import type { ComponentPublicInstance } from 'vue';
import { describe, expect, it, afterEach, vi } from 'vitest';
import Password from '@/editor/Components/Password.vue';

describe('EditorPassword', () => {
    let wrapper: VueWrapper<ComponentPublicInstance> | null = null;

    afterEach(() => {
        if (wrapper) {
            wrapper.unmount();
        }
        wrapper = null;
        vi.unstubAllGlobals();
    });

    it('does not render false optional field metadata as input attributes', () => {
        wrapper = mount(Password, {
            props: {
                id: 'login_password',
                name: 'login[password]',
                value: false,
                errormessage: false,
                pattern: false,
                placeholder: false,
            },
        });

        const input = wrapper.get('input');

        expect((input.element as HTMLInputElement).value).toBe('');
        expect(input.attributes('data-errormessage')).toBeUndefined();
        expect(input.attributes('pattern')).toBeUndefined();
        expect(input.attributes('placeholder')).toBeUndefined();
    });

    it('renders string field metadata as input attributes', () => {
        wrapper = mount(Password, {
            props: {
                id: 'login_password',
                name: 'login[password]',
                value: 'hunter2',
                required: true,
                readonly: true,
                errormessage: 'Error!',
                pattern: '.{8,}',
                placeholder: 'Password…',
            },
        });

        const input = wrapper.get('input');
        expect((input.element as HTMLInputElement).value).toBe('hunter2');
        expect(input.attributes('id')).toBe('login_password');
        expect(input.attributes('name')).toBe('login[password]');
        expect(input.attributes('required')).toBeDefined();
        expect(input.attributes('readonly')).toBeDefined();
        expect(input.attributes('data-errormessage')).toBe('Error!');
        expect(input.attributes('pattern')).toBe('.{8,}');
        expect(input.attributes('placeholder')).toBe('Password…');
        expect(input.attributes('autocomplete')).toBe('new-password');
    });

    it('reveals the password on mount unless hidden', async () => {
        wrapper = mount(Password, { props: { name: 'pw' } });
        await wrapper.vm.$nextTick();

        expect(wrapper.get('input').attributes('type')).toBe('text');
        expect(wrapper.get('.toggle-password').classes()).toContain('fa-eye-slash');
    });

    it('keeps the password masked on mount when hidden', () => {
        wrapper = mount(Password, { props: { name: 'pw', hidden: true } });

        expect(wrapper.get('input').attributes('type')).toBe('password');
        expect(wrapper.get('.toggle-password').classes()).toContain('fa-eye');
    });

    it('toggles the visibility back and forth via the eye icon', async () => {
        wrapper = mount(Password, { props: { name: 'pw', hidden: true } });
        const icon = wrapper.get('.toggle-password');

        await icon.trigger('click');
        expect(wrapper.get('input').attributes('type')).toBe('text');
        expect(icon.classes()).toContain('fa-eye-slash');

        await icon.trigger('click');
        expect(wrapper.get('input').attributes('type')).toBe('password');
        expect(icon.classes()).toContain('fa-eye');
    });

    it('shows no strength meter by default', async () => {
        const zxcvbn = vi.fn(() => ({ score: 3 }));
        vi.stubGlobal('zxcvbn', zxcvbn);
        wrapper = mount(Password, { props: { name: 'pw', hidden: true } });

        await wrapper.get('input').setValue('hunter2');

        expect(wrapper.find('.progress').exists()).toBe(false);
        expect(zxcvbn).not.toHaveBeenCalled();
    });

    it('measures the strength of the typed password', async () => {
        vi.stubGlobal(
            'zxcvbn',
            vi.fn(() => ({ score: 3 })),
        );
        wrapper = mount(Password, {
            props: { name: 'pw', hidden: true, strength: true },
        });

        const input = wrapper.get('input');
        (input.element as HTMLInputElement).value = 'hunter2';
        await input.trigger('input');

        const bar = wrapper.get('.progress-bar');
        expect(bar.attributes('aria-valuenow')).toBe('3');
        expect(bar.attributes('aria-valuemax')).toBe('4');
        expect(bar.attributes('style')).toBe('width: 75%;');
    });

    it('measures the initial value on mount when strength is enabled', async () => {
        const zxcvbn = vi.fn(() => ({ score: 2 }));
        vi.stubGlobal('zxcvbn', zxcvbn);
        wrapper = mount(Password, {
            props: {
                name: 'pw',
                hidden: true,
                strength: true,
                value: 'hunter2',
            },
        });
        await wrapper.vm.$nextTick();

        expect(zxcvbn).toHaveBeenCalledWith('hunter2');
        expect(wrapper.get('.progress-bar').attributes('aria-valuenow')).toBe('2');
    });
});
