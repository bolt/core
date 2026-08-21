import { describe, expect, it } from 'vitest';
import { resetModalContent } from '@/modal';

/**
 * Puts the shared resources modal back to its loading state after a field has
 * written its own content into it.
 */

const givenModal = () => {
    document.body.insertAdjacentHTML(
        'beforeend',
        '<div id="resourcesModal"><div class="modal-content">field content</div></div>',
    );
};

const modalContent = () => document.querySelector('#resourcesModal .modal-content')!;

describe('resetModalContent', () => {
    it('replaces whatever a field left behind with the loading state', () => {
        givenModal();

        resetModalContent({ modal_button_deny: 'Cancel', modal_button_save: 'Save' });

        expect(modalContent().innerHTML).not.toContain('field content');
        expect(modalContent().querySelectorAll('.spinner-border')).toHaveLength(2);
        expect(modalContent().querySelector('.modal-header')).not.toBeNull();
        expect(modalContent().querySelector('.modal-body')).not.toBeNull();
        expect(modalContent().querySelector('.modal-footer')).not.toBeNull();
    });

    it('labels the buttons with the translations it is given', () => {
        givenModal();

        resetModalContent({ modal_button_deny: 'Annuleren', modal_button_save: 'Opslaan' });

        expect(modalContent().querySelector('#modalButtonDeny')!.textContent).toBe('Annuleren');
        expect(modalContent().querySelector('#modalButtonAccept')!.textContent).toBe('Opslaan');
    });

    it('falls back to english when no translations are given', () => {
        givenModal();

        resetModalContent({});

        expect(modalContent().querySelector('#modalButtonDeny')!.textContent).toBe('Close');
        expect(modalContent().querySelector('#modalButtonAccept')!.textContent).toBe('Save');
    });

    it('falls back to english when only one translation is given', () => {
        givenModal();

        resetModalContent({ modal_button_deny: 'Annuleren' });

        expect(modalContent().querySelector('#modalButtonDeny')!.textContent).toBe('Close');
    });

    it('leaves both buttons able to dismiss the modal', () => {
        givenModal();

        resetModalContent({ modal_button_deny: 'Cancel', modal_button_save: 'Save' });

        const buttons = modalContent().querySelectorAll('.modal-footer button');
        expect(buttons).toHaveLength(2);
        buttons.forEach(button => expect(button.getAttribute('data-bs-dismiss')).toBe('modal'));
    });
});
