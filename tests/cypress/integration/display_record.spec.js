/// <reference types="cypress" />

describe('As a user I want to display a single record', () => {
    it('checks if a record exists', () => {
        cy.visit('/entry/this-is-a-record-in-the-entries-contenttype');
        cy.get('.title').should('have.length', 1);
        cy.get('.edit-link').should('not.exist');
    })
});

describe('As an admin I want to see edit link on single record page', () => {
    it('checks if an admin can edit a record', () => {
        cy.login();
        cy.visit('/entry/this-is-a-record-in-the-entries-contenttype');
        cy.get('.title').should('have.length', 1);
        cy.get('.edit-link').should('contain', 'Edit');
    })
});

describe('As a user I want to see the difference between records with a "Title" and a "Heading"', () => {
    it('checks if heading replaces title', () => {
        cy.visit('/page/2');
        cy.get('.heading').should('have.length', 1);
        cy.get('.title').should('not.exist');
    })
});

describe('As a user I want to see the correct canonical URL for a page', () => {
    it('checks for correct URL', () => {
        cy.visit('/page/this-is-a-page');
        cy.get("link[rel='canonical']").should('have.attr', 'href', Cypress.config().baseUrl + '/page/this-is-a-page');

        cy.visit('/page/2');
        cy.get("link[rel='canonical']").should('have.attr', 'href', Cypress.config().baseUrl + '/page/this-is-a-page');

        cy.visit('/en/page/this-is-a-page');
        cy.get("link[rel='canonical']").should('have.attr', 'href', Cypress.config().baseUrl + '/page/this-is-a-page');

        cy.visit('/nl/page/2');
        cy.get("link[rel='canonical']").should('have.attr', 'href', Cypress.config().baseUrl + '/nl/page/this-is-a-page');
    })
});
