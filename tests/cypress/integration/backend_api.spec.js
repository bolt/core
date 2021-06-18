/// <reference types="cypress" />

describe('As an admin, I want to see the API documentation page', () => {
    it('checks that the API page exists', () => {
        cy.login();
        cy.visit('/bolt/api');
        cy.get('.admin__header--title').should('contain', 'Bolt API');
    })
});
