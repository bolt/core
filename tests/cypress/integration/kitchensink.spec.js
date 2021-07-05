/// <reference types="cypress" />

describe('As an admin I want to see the Kitchensink page', () => {
    it('checks that the Kitchensink page exists and works', () => {
        cy.login();
        cy.visit('/bolt/kitchensink');
        cy.get('.admin__header--title').should('contain', 'Kitchensink');
        cy.get('.admin__header--title').should('contain', 'different things');
        cy.get('h2').should('contain', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit');
        cy.get('h3').should('contain', 'different things');

        cy.get('section.buttons button.btn').its('length').should('eq', 20);

        cy.get('input[name="foo"]').should('have.value', 'FooBar');

        cy.get("label[for='field-title']").should('contain', 'Title:');
        cy.get('input#field-title').should('exist');
        cy.get('div#field-title_postfix').should('contain', 'shown on the homepage');
    })
});
