/// <reference types="cypress" />

describe('As an admin I want to see Translations page', () => {
    it('checks that the translations page exists', () => {
        cy.login();
        cy.visit('/bolt/_trans');
        cy.get('.admin__header--title').should('contain', 'Edit Translations');
    })
});
