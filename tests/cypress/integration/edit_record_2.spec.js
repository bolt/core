/// <reference types="cypress" />

describe('As an Admin I want to view saved changes of a record', () => {
    it('checks if an admin can view saved changes on a record', () => {
        cy.login();
        cy.visit('/bolt/edit/2');
        cy.get('input[id="field-heading"]').clear();
        cy.get('input[id="field-heading"]').type('This is the title in the wrong locale');
        cy.get('button[class="btn btn-success mb-0 "]').eq(1).scrollIntoView();
        cy.get('button[class="btn btn-success mb-0 "]').eq(1).click();

        cy.visit('/bolt/edit/2?edit_locale=nl');
        cy.get('input[id="field-heading"]').clear();
        cy.get('input[id="field-heading"]').type('This is the title in the right locale');
        cy.get('button[class="btn btn-success mb-0 "]').eq(1).scrollIntoView();
        cy.get('button[class="btn btn-success mb-0 "]').eq(1).click();

        cy.url().should('contain', '/bolt/edit/2?edit_locale=nl');
        cy.get('a[class="btn btn-tertiary btn-sm"]').scrollIntoView();
        cy.get('a[class="btn btn-tertiary btn-sm"]').click();

        cy.visit('/nl/page/this-is-a-page');
        cy.get('h1').should('not.contain', 'This is the title in the wrong locale');
        cy.get('h1').should('contain', 'This is the title in the right locale');
    })
});

describe('As an Admin I want to preview an edited record', () => {
    it('checks if an admin can preview an edited record', () => {
        cy.login();
        cy.visit('/bolt/edit/30');
        cy.get('input[id="field-title"]').clear();
        cy.get('input[id="field-title"]').type('Check preview');


        // Preview cannot be easily tested by pressing buttons.
        // Instead, we need to serialize and submit manually.
        // See https://github.com/cypress-io/cypress/issues/6251#issuecomment-882386283
        cy.get('#editor :input').then(($el) => {
            const jqueryForm = Cypress.dom.wrap($el);
            const data = jqueryForm.serialize();
            cy.request({method: 'POST', url: '/bolt/preview/30', body: data, form: true, failOnStatusCode: false})
                .its('body')
                .should('contain', 'Check preview');
        });

        // Now back to the "original" window...
        cy.reload();
        cy.wait(1000);
        cy.get('input[id="field-title"]').should('not.have.value', 'Check preview');
    });
});
