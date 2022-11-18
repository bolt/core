/// <reference types="cypress" />

describe('Edit content as chief editor and editor without being the owner', () => {
    it('checks that the chief editor and editor can edit someone else\'s content', () => {
        cy.login('jane_chief', 'jane%1');

        cy.visit('/bolt/content/pages');

        cy.get('.listing__row.is-normal').eq(0).children('.listing__row--item.is-actions.edit-actions').children('div').children('a[href="/bolt/edit/2"]').click();
        cy.url().should('contain', '/bolt/edit/2');

        cy.get('#field-heading').invoke('val').should('contain', 'This is a page');
        cy.get('#field-heading').clear();
        cy.get('#field-heading').type('This is an edited page');

        cy.get('button[name="save"]').eq(1).scrollIntoView();
        cy.get('button[name="save"]').eq(1).click();

        cy.get('#field-heading').invoke('val').should('contain', 'This is an edited page');
        cy.visit('bolt/logout');

        cy.login('john_editor', 'john%1');

        cy.visit('/bolt/content/pages');

        cy.get('.listing__row.is-normal').eq(0).children('.listing__row--item.is-actions.edit-actions').children('div').children('a[href="/bolt/edit/2"]').click();
        cy.url().should('contain', '/bolt/edit/2');

        cy.get('#field-heading').invoke('val').should('contain', 'This is an edited page');
        cy.get('#field-heading').clear();
        cy.get('#field-heading').type('This is a page');

        cy.get('button[name="save"]').eq(1).scrollIntoView();
        cy.get('button[name="save"]').eq(1).click();

        cy.get('#field-heading').invoke('val').should('contain', 'This is a page');
        cy.visit('bolt/logout');
    })
});
