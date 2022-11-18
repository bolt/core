/// <reference types="cypress" />

describe('As an Admin I want to sort content', () => {
    it('checks that an admin can sort content', () => {
        cy.login();
        cy.visit('/bolt');

        cy.findAllByText('Entries').click();
        cy.url().should('contain', '/bolt/content/entries');
        cy.get('.admin__header--title').should('contain', 'Entries');
        cy.get('div[class="card-header"]').should('contain', 'Contentlisting');

        cy.get('select[name="sortBy"]').select('author', { force: true });
        cy.get('button[class="btn btn-secondary mb-0 "]').should('contain', 'Filter').click();

        cy.url().should('contain', '/bolt/content/entries?sortBy=author&filter=');
        cy.get('.listing__row--list').eq(0).find('li').eq(1).should('contain', 'Admin');
        cy.get('.listing__row--list').eq(5).find('li').eq(1).should('not.contain', 'Admin');
    })
});
