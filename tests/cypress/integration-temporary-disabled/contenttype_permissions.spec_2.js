/// <reference types="cypress" />

describe('Create content as editor and delete it as chief editor', () => {
    it('checks that editors can create content and chief editors can delete it', () => {
        cy.login('john_editor', 'john%1');

        cy.visit('/bolt/content/pages');

        cy.get('.card.mb-3').children('.card-body').children('p').children('a').click();
        cy.url().should('contain', '/bolt/new/pages');

        cy.get('#field-heading').type('Test heading');

        cy.get('button[name="save"]').eq(1).scrollIntoView();
        cy.get('button[name="save"]').eq(1).should('be.visible').click({force:true});

        cy.visit('/bolt/logout');

        cy.login('jane_chief', 'jane%1');

        cy.visit('/bolt/content/pages?page=3');

        cy.get('.listing__row.is-normal').eq(2).children('.listing__row--item.is-details').children('a').should('contain', 'Test heading -');
        cy.get('button[data-bs-toggle="dropdown"]').eq(3).click();
        cy.get('.edit-actions__dropdown.dropdown-menu.dropdown-menu-right').eq(2).children('a').eq(4).click();
        cy.get('button[data-bs-dismiss="modal"]').click({ multiple: true });

        cy.visit('/bolt/content/pages?page=3');
        cy.get('.listing--container').its('length').should('eq', 3);
    })
});
