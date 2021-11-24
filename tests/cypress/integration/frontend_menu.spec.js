/// <reference types="cypress" />

describe('As a user I want to see the menu\'s in the frontend', () => {
    it('checks if the frontend menu exists', () => {
        cy.visit(Cypress.config().baseUrl);
        cy.get('.menu .first').should('contain', 'Home');
        cy.get('.menu .bolt-site').should('contain', 'The Bolt site');
        cy.get('.has-submenu li').its('length').should('eq', 4);
    })

    it('checks if the multi-level frontend menu exists', () => {
        cy.visit('/test/title-of-the-test');
        cy.get('.menu .item-1').should('contain', 'Item 1');
        cy.get('.menu .item-1-1').should('contain', 'Item 1.1');
        cy.get('.menu .item-1-1-2').should('contain', 'Item 1.1.2');
        cy.get('.menu .item-1-1-2-2').should('contain', 'Item 1.1.2.2');
    })
});
