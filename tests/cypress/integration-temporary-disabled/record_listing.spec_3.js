/// <reference types="cypress" />

describe('As an Admin I want to filter content', () => {
    it('checks that an admin can filter content', () => {
        cy.login();
        cy.visit('/bolt/content/entries');

        cy.get('div[class="card-header"]').should('contain', 'Contentlisting');

        cy.get('#content-filter').type('a', { force: true });
        cy.get('button[class="btn btn-secondary mb-0 "]').should('contain', 'Filter').click();

        cy.url().should('contain', '/bolt/content/entries?sortBy=&filter=a');
        cy.get('.listing--container').its('length').should('eq', 10);

        cy.wait(1000);

        cy.get('#content-filter').clear();
        cy.get('#content-filter').type('Entries');
        cy.get('button[class="btn btn-secondary mb-0 "]').should('contain', 'Filter').click();
        cy.url().should('contain', '/bolt/content/entries?sortBy=&filter=Entries');
        cy.get('.listing--container').find('div[class="listing__row is-normal"]').find('div[class="listing__row--item is-details"]').find('a').should('contain', 'Entries');

        cy.wait(1000);

        cy.get('#content-filter').clear();
        cy.get('#content-filter').type(' ');
        cy.get('button[class="btn btn-secondary mb-0 "]').should('contain', 'Filter').click();
        cy.url().should('contain', '/bolt/content/entries?sortBy=&filter=');
        cy.get('.listing--container').its('length').should('eq', 10);
    })

    it('checks that a user can see the contenttype listing', () => {
        cy.visit('/pages');
        cy.get('article').its('length').should('eq', 6);
    })
});
