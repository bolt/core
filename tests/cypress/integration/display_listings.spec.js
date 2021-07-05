/// <reference types="cypress" />

describe('As an admin I want to see Dashboard page', () => {
    it('checks that the dashboard listings work', () => {

        cy.login();
        cy.get('.admin__header--title').should('contain', 'Dashboard');
        cy.get('.listing__row').should('exist');
        cy.get('.listing__row').its('length').should('eq', 8)
    })
});