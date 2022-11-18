/// <reference types="cypress" />

describe('Change content post status as chief editor', () => {
    it('checks that the chief editor can change a post\'s status', () => {
        cy.login('jane_chief', 'jane%1');

        cy.visit('/bolt/content/pages');

        cy.get('button[data-bs-toggle="dropdown"]').eq(0).click();
        cy.get('.edit-actions__dropdown.dropdown-menu.dropdown-menu-right').eq(0).children('a').eq(1).click({force: true});

        cy.get('.status.is-held').should('exist');

        cy.get('button[data-bs-toggle="dropdown"]').eq(0).click();
        cy.get('.edit-actions__dropdown.dropdown-menu.dropdown-menu-right').eq(0).children('a').eq(0).click({force: true});

        cy.get('.listing--container').eq(0).children('.listing__row.is-normal').children('.listing__row--item.is-meta').children('.status.is-held').should('not.exist');
    })
});
