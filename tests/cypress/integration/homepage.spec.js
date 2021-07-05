/// <reference types="cypress" />

describe('I want to display Homepage', () => {
    it('checks that the homepage exists', () => {
        cy.visit('/');
        cy.get('h2').should('contain', 'Bolt Core Git Clone');
        cy.get('h5').should('contain', 'Recent Pages');
    })
});