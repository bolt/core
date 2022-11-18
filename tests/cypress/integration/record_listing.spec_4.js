/// <reference types="cypress" />

describe('As an Admin I want to expand and compact the contenttype listing', () => {
    it('checks that an admin can expand and compact the contenttype listing', () => {
        cy.login();
        cy.visit('/bolt/content/pages');
        cy.get('button[title="Expanded"]').should('exist');
        cy.get('button[title="Compact"]').should('exist');

        cy.get('button[title="Compact"]').click();
        cy.get('div[class="listing__row--item is-thumbnail"]').should('not.exist');
        cy.get('span[class="listing__row--item-title-excerpt"]').should('not.be.visible');
        cy.wait(3000);

        cy.get('button[title="Expanded"]').click();
        cy.get('div[class="listing__row--item is-thumbnail"]').should('be.visible');
        cy.get('span[class="listing__row--item-title-excerpt"]').should('be.visible');
    })
});
