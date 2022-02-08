/// <reference types="cypress" />

describe('As a user I want to see taxonomies in records and listings', () => {
    it('checks if there are taxonomies on a record', () => {
        cy.visit('/entry/this-is-a-record-in-the-entries-contenttype');
        cy.get('.title').should('have.length', 1);
        cy.get('.taxonomy-categories').its('length').should('eq', 2);
        cy.get('.taxonomy-tags').its('length').should('eq', 4);
    })

    it('checks if there is a listing of a taxonomy', () => {
        cy.login();
        cy.visit('/entry/this-is-a-record-in-the-entries-contenttype');
        cy.get('.title').should('have.length', 1);
        cy.get('.taxonomy-categories').eq(0).scrollIntoView();
        cy.findByText('love').click();
        cy.visit('/categories/movies');
        cy.get('article').its('length').should('be.gte', 3);
    });
});
