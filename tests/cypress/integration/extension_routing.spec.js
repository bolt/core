/// <reference types="cypress" />

describe('I want to see a page, added by an Extension', () => {
    it('checks that extension pages exist', () => {
        cy.visit('/extensions/reference/Zebedeus');
        cy.get('p').should('contain', 'Hello, Zebedeus');

        cy.visit('/extensions/reference', {failOnStatusCode: false});
        cy.get('h1').should('contain', '404 Page not found');
    })
});
