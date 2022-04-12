/// <reference types="cypress" />

describe('As an admin I want to see the News Widget', () => {
    it('checks if News widget exists', () => {
        cy.login();
        cy.visit('/bolt/');
        cy.get('#widget-news-widget').should('contain', 'Latest Bolt News');
    })
});
