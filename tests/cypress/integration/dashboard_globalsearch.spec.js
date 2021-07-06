/// <reference types="cypress" />

describe('As an Admin I want to filter content', () => {
    it('checks that content filtering works as an admin', () => {
        cy.login();
        cy.visit('/bolt');

        cy.get('.admin__header--title__prefix').should('contain', 'Bolt Dashboard');

        cy.get('#global-search').type('a');
        cy.get('button[title="Search"]').click();

        cy.url().should('contain', '/bolt/?filter=a'); 
        cy.get('.listing--container').its('length').should('eq', 8);
        cy.get('h1').should('contain', "All content, filtered by 'a'");

        cy.wait(1000);

        cy.get('#global-search').clear();
        cy.get('#global-search').type('Entries');
        cy.get('button[title="Search"]').click();
        cy.url().should('contain', '/bolt/?filter=Entries'); 
        cy.get('.listing--container').its('length').should('eq', 1);
        cy.get('.listing--container').should('contain', "Entries");

        cy.wait(1000);

        cy.get('#global-search').clear();
        cy.get('#global-search').type(' ');
        cy.get('button[title="Search"]').click();
        cy.url().should('contain', '/bolt'); 
        cy.get('.listing--container').its('length').should('eq', 8);
    })
});
