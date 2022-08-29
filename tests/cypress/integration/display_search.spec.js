/// <reference types="cypress" />

describe('As a user I want to display search results', () => {
    it('checks that search results are displayed as a user', () => {
        cy.visit('/');
        cy.get('input[type="search"]').scrollIntoView().type('consequatur');
        cy.get('button[type="submit"]').click();

        cy.url().should('include', '/search');
        cy.get('.search-results').should('contain', "Search results for 'consequatur'.");
        cy.get('article').its('length').should('be.gte', 3);

        cy.get('input[name="searchTerm"]').eq(0).clear();
        cy.get('input[name="searchTerm"]').eq(0).type('ymnrubeyrvwearsytevsf');
        cy.get('button[type="submit"]').eq(0).click();

        cy.url().should('include', '/search');
        cy.get('.search-results').should('have.text', "Search results for 'ymnrubeyrvwearsytevsf'.");
        cy.get('.search-results-description').should('contain', "No search results found for 'ymnrubeyrvwearsytevsf'.");
        cy.get('article').should('not.exist');

        cy.get('input[name="searchTerm"]').eq(0).clear();
        cy.get('input[name="searchTerm"]').eq(0).type(' ');
        cy.get('button[type="submit"]').eq(0).click();

        cy.url().should('include', '/search');
        cy.get('.search-results-description').should('contain', "Please provide a search term, in order to display relevant results.");
        cy.get('article').should('not.exist');
    })
});
