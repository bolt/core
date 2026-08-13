/// <reference types="cypress" />

describe('As an Admin I want to save a record after previewing it', () => {
    beforeEach(() => {
        cy.login();
        cy.visit('/bolt/edit/1');

        // The preview button submits the edit form with `formtarget="_blank"`, and Cypress can not
        // drive the tab that opens. Cancelling the navigation still lets the form's own submit
        // handlers run first, which is the behaviour under test. See issue #3778. The flag records
        // that the submit really happened, so the assertions below can not pass vacuously.
        cy.window().then(win => {
            win.previewSubmitted = false;
            win.document
                .getElementById('editcontent')
                .addEventListener('submit', event => {
                    if (event.submitter && event.submitter.id === 'button-preview') {
                        win.previewSubmitted = true;
                        event.preventDefault();
                    }
                });
        });
    });

    it('checks that previewing leaves the save button enabled', () => {
        cy.get('button[id="button-preview"]')
            .eq(1)
            .scrollIntoView();
        cy.get('button[id="button-preview"]')
            .eq(1)
            .click();

        cy.window()
            .its('previewSubmitted')
            .should('be.true');

        // Deliberately without `.eq()`: neither of the two save buttons may be disabled.
        cy.get('button[name="save"]').should('not.be.disabled');
    });

    it('checks that an admin can still save a record after previewing it', () => {
        cy.get('button[id="button-preview"]')
            .eq(1)
            .scrollIntoView();
        cy.get('button[id="button-preview"]')
            .eq(1)
            .click();

        // Assert before saving: the save navigates away, taking the flag with it.
        cy.window()
            .its('previewSubmitted')
            .should('be.true');

        cy.get('button[name="save"]')
            .eq(1)
            .scrollIntoView();
        cy.get('button[name="save"]')
            .eq(1)
            .click();

        cy.get('.toast-body').should('contain', 'Content updated successfully');
    });

    it('checks that saving still disables the save button, to prevent double submits', () => {
        // Keep the page from navigating away, so the button can be inspected after the submit.
        cy.window().then(win => {
            win.document
                .getElementById('editcontent')
                .addEventListener('submit', event => event.preventDefault());
        });

        cy.get('button[name="save"]')
            .eq(1)
            .scrollIntoView();
        cy.get('button[name="save"]')
            .eq(1)
            .click();

        cy.get('button[name="save"]')
            .eq(1)
            .should('be.disabled');
    });
});
