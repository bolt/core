/// <reference types="cypress" />

describe('As an Admin I want to view saved changes of a record or preview these', () => {
    it('checks if an admin can view saved changes on a record', () => {
        cy.login();
        cy.visit('/bolt/edit/2');
        cy.get('input[id="field-heading"]').clear();
        cy.get('input[id="field-heading"]').type('This is the title in the wrong locale');
        cy.get('button[class="btn btn-success mb-0"]').eq(1).scrollIntoView();
        cy.get('button[class="btn btn-success mb-0"]').eq(1).click();

        cy.visit('/bolt/edit/2?edit_locale=nl');
        cy.get('input[id="field-heading"]').clear();
        cy.get('input[id="field-heading"]').type('This is the title in the right locale');
        cy.get('button[class="btn btn-success mb-0"]').eq(1).scrollIntoView();
        cy.get('button[class="btn btn-success mb-0"]').eq(1).click();

        cy.url().should('contain', '/bolt/edit/2?edit_locale=nl');
        cy.get('a[class="btn btn-tertiary btn-sm"]').scrollIntoView();
        cy.get('a[class="btn btn-tertiary btn-sm"]').click();

        cy.visit('/nl/page/this-is-a-page');
        cy.get('h1').should('not.contain', 'This is the title in the wrong locale');
        cy.get('h1').should('contain', 'This is the title in the right locale');
    })

    it('checks if an admin can preview an edited record', () => {
        cy.login();
        cy.visit('/bolt/edit/30');
        cy.get('input[id="field-title"]').clear();
        cy.get('input[id="field-title"]').type('Check preview');
        
        cy.get('#button-preview').invoke('removeAttr', 'formtarget').click({force: true});
        cy.url().should('contain', '/preview/30');
        cy.get('body').should('contain', 'Check preview');
        cy.visit('/bolt/edit/30');
        cy.get('input[id="field-title"]').should('not.have.value', 'Check preview');
    });
});
