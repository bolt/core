import { mount } from '@vue/test-utils';
import { nextTick } from 'vue';
import { afterEach, describe, expect, it, vi } from 'vitest';
import Password from '@/editor/Components/Password.vue';

const defaultProps = {
    value: '',
    name: 'fields[password]',
    id: 'field-password',
    hidden: true,
    strength: false,
    required: false,
    readonly: false,
};

// togglePassword looks its own input up through jQuery by id, so the component
// has to be in the document rather than detached.
const mountPassword = (props: Record<string, unknown> = {}) =>
    mount(Password, { propsData: { ...defaultProps, ...props }, attachTo: document.body });

const input = (wrapper: ReturnType<typeof mount>) => wrapper.find('input');
const toggle = (wrapper: ReturnType<typeof mount>) => wrapper.find('.toggle-password');
const strengthBar = (wrapper: ReturnType<typeof mount>) => wrapper.find('.progress-bar');

const stubZxcvbn = (score: number) => {
    const zxcvbn = vi.fn(() => ({ score }));
    vi.stubGlobal('zxcvbn', zxcvbn);
    return zxcvbn;
};

afterEach(() => {
    vi.unstubAllGlobals();
});

describe('EditorPassword', () => {
    it('renders a password input carrying the field metadata', () => {
        const wrapper = mountPassword({
            value: 'secret',
            required: true,
            errormessage: 'Required',
            pattern: '.{8,}',
            placeholder: 'Your password',
        });

        expect(input(wrapper).attributes('id')).toBe('field-password');
        expect(input(wrapper).attributes('name')).toBe('fields[password]');
        expect((input(wrapper).element as HTMLInputElement).value).toBe('secret');
        expect(input(wrapper).attributes('required')).toBeDefined();
        expect(input(wrapper).attributes('data-errormessage')).toBe('Required');
        expect(input(wrapper).attributes('pattern')).toBe('.{8,}');
        expect(input(wrapper).attributes('placeholder')).toBe('Your password');
        expect(input(wrapper).attributes('autocomplete')).toBe('new-password');
    });

    it('omits optional attributes when unset', () => {
        const wrapper = mountPassword();

        expect(input(wrapper).attributes('required')).toBeUndefined();
        expect(input(wrapper).attributes('readonly')).toBeUndefined();
        expect(input(wrapper).attributes('pattern')).toBeUndefined();
    });

    it('keeps the password masked when it is marked hidden', () => {
        const wrapper = mountPassword({ hidden: true });

        expect(input(wrapper).attributes('type')).toBe('password');
        expect(toggle(wrapper).classes()).toContain('fa-eye');
    });

    it('reveals the password on mount when it is not marked hidden', () => {
        // mounted() clicks the toggle itself, so a field that is not hidden
        // starts out readable.
        const wrapper = mountPassword({ hidden: false });

        expect(input(wrapper).attributes('type')).toBe('text');
        expect(toggle(wrapper).classes()).toContain('fa-eye-slash');
    });

    it('toggles between masked and readable', async () => {
        const wrapper = mountPassword({ hidden: true });

        await toggle(wrapper).trigger('click');
        expect(input(wrapper).attributes('type')).toBe('text');
        expect(toggle(wrapper).classes()).toContain('fa-eye-slash');

        await toggle(wrapper).trigger('click');
        expect(input(wrapper).attributes('type')).toBe('password');
        expect(toggle(wrapper).classes()).toContain('fa-eye');
    });

    it('hides the strength meter unless asked for', () => {
        const wrapper = mountPassword({ strength: false });

        expect(wrapper.find('.progress').exists()).toBe(false);
    });

    it('shows an empty strength meter out of four', () => {
        stubZxcvbn(0);
        const wrapper = mountPassword({ strength: true });

        expect(strengthBar(wrapper).attributes('aria-valuemax')).toBe('4');
        expect(strengthBar(wrapper).attributes('aria-valuenow')).toBe('0');
    });

    it('scores the password as it is typed', async () => {
        const zxcvbn = stubZxcvbn(3);
        const wrapper = mountPassword({ strength: true });

        await input(wrapper).setValue('correcthorsebatterystaple');
        await nextTick();

        expect(zxcvbn).toHaveBeenCalledWith('correcthorsebatterystaple');
        expect(strengthBar(wrapper).attributes('aria-valuenow')).toBe('3');
    });

    it('scores an existing password on mount', async () => {
        const zxcvbn = stubZxcvbn(4);
        const wrapper = mountPassword({ value: 'existing-secret', strength: true });
        await nextTick();

        expect(zxcvbn).toHaveBeenCalledWith('existing-secret');
        expect(strengthBar(wrapper).attributes('aria-valuenow')).toBe('4');
    });

    it('does not score anything when the meter is off', async () => {
        const zxcvbn = stubZxcvbn(2);
        const wrapper = mountPassword({ strength: false });

        await input(wrapper).setValue('anything');

        expect(zxcvbn).not.toHaveBeenCalled();
    });
});
