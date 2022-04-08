/// <reference types="cypress" />

describe('As a user I want to display a single record', () => {
    it('checks if a record exists', () => {
        cy.visit('/entry/this-is-a-record-in-the-entries-contenttype');
        cy.get('.title').should('have.length', 1);
        cy.get('.edit-link').should('not.exist');
    });

    it('checks if an admin can edit a record', () => {
        cy.login();
        cy.visit('/entry/this-is-a-record-in-the-entries-contenttype');
        cy.get('.title').should('have.length', 1);
        cy.get('.edit-link').should('contain', 'Edit');
    });

    it('checks if you can see the difference between records with a Title and a Heading', () => {
        cy.visit('/page/2');
        cy.get('.heading').should('have.length', 1);
        cy.get('.title').should('not.exist');
    });

    it('checks for correct canonical URL', () => {
        cy.visit('/page/this-is-a-page');
        cy.get("link[rel='canonical']").should('have.attr', 'href', 'https://example.org/page/this-is-a-page');

        cy.visit('/page/2');
        cy.get("link[rel='canonical']").should('have.attr', 'href', 'https://example.org/page/this-is-a-page');

        cy.visit('/en/page/this-is-a-page');
        cy.get("link[rel='canonical']").should('have.attr', 'href', 'https://example.org/page/this-is-a-page');

        cy.visit('/nl/page/2');
        cy.get("link[rel='canonical']").should('have.attr', 'href', 'https://example.org/nl/page/this-is-a-page');
    });
});
