import { afterEach, describe, expect, it, vi } from 'vitest';
import $ from 'jquery';
import { patience_virtue, renable } from '@/patience-is-a-virtue';

/**
 * Turns a button's icon into a spinner while a slow action runs, and puts it
 * back afterwards. Twig marks the buttons with `data-patience="virtue"`.
 */

const givenButton = (iconClass: string) => {
    document.body.insertAdjacentHTML('beforeend', `<button class="btn"><i class="${iconClass}"></i> Save</button>`);
    return $('button').last();
};

afterEach(() => {
    vi.useRealTimers();
});

describe('patience_virtue', () => {
    it('swaps the icon for a spinner', () => {
        const button = givenButton('fas fa-save');

        patience_virtue(button);

        expect(button.find('i').attr('class')).toBe('fas fa-w fa-cog fa-spin ');
    });

    it('remembers the original icon so it can be restored', () => {
        const button = givenButton('fas fa-save');

        patience_virtue(button);

        expect(button.attr('data-original-class')).toBe('fas fa-save');
    });

    it('keeps the icon spacing so the button does not jump', () => {
        const button = givenButton('fas fa-save mt-2');

        patience_virtue(button);

        expect(button.find('i').attr('class')).toBe('fas fa-w fa-cog fa-spin mt-2');
    });

    it('recognises the bootstrap 4 spacing shorthands', () => {
        for (const spacing of ['mt-1', 'pb-3', 'mx-0', 'py-5', 'ml-4', 'pr-2']) {
            document.body.innerHTML = '';
            const button = givenButton(`fas fa-save ${spacing}`);

            patience_virtue(button);

            expect(button.find('i').attr('class')).toBe(`fas fa-w fa-cog fa-spin ${spacing}`);
        }
    });

    it('drops bootstrap 5 start/end spacing, which is what this codebase uses', () => {
        // Pinning current behaviour, which is a bug. The regex is
        // /[mp][tblrxy]-[0-5]/ — it knows Bootstrap 4's l/r sides but not
        // Bootstrap 5's `ms-`/`me-`. Since the templates were moved to
        // Bootstrap 5, nearly every real icon (`fas fa-fw me-2`, `fa-w me-0`)
        // loses its spacing when it becomes a spinner, and the button contents
        // shift while saving.
        for (const spacing of ['ms-2', 'me-2', 'ps-1', 'pe-3']) {
            document.body.innerHTML = '';
            const button = givenButton(`fas fa-save ${spacing}`);

            patience_virtue(button);

            expect(button.find('i').attr('class')).toBe('fas fa-w fa-cog fa-spin ');
        }
    });

    it('disables the button shortly after, so the click still registers', () => {
        vi.useFakeTimers();
        const button = givenButton('fas fa-save');

        patience_virtue(button);
        expect(button.attr('disabled')).toBeUndefined();

        vi.advanceTimersByTime(50);

        expect(button.attr('disabled')).toBe('disabled');
    });
});

describe('renable', () => {
    it('restores the icon and re-enables the button', () => {
        vi.useFakeTimers();
        const button = givenButton('fas fa-save');
        patience_virtue(button);
        vi.advanceTimersByTime(50);

        renable();

        expect(button.find('i').attr('class')).toBe('fas fa-save');
        expect(button.attr('disabled')).toBeUndefined();
    });

    it('restores every waiting button at once', () => {
        const first = givenButton('fas fa-save');
        const second = givenButton('fas fa-upload');
        patience_virtue(first);
        patience_virtue(second);

        renable();

        expect(first.find('i').attr('class')).toBe('fas fa-save');
        expect(second.find('i').attr('class')).toBe('fas fa-upload');
    });

    it('leaves buttons that were never spinning alone', () => {
        const button = givenButton('fas fa-save');

        renable();

        expect(button.find('i').attr('class')).toBe('fas fa-save');
    });
});
