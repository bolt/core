/// <reference types="cypress" />

describe('No localization link for contenttype without locales', () => {
    it('checks that there\'s no localization link for contentype without locales', () => {
        cy.login();

        cy.get('a[href="/bolt/content/entries"]').eq(1).click();
        cy.get('a[href="/bolt/edit/23"]').eq(1).click();

        cy.url().should('contain', '/bolt/edit/23');
        cy.get('h1').find('span').should('contain', 'Edit Entry');
        cy.get('#multiselect-localeswitcher').should('not.exist');
    })
});

describe('See localization link for contenttype with locales', () => {
    it('checks that there\'s a localization link for contentype with locales', () => {
        cy.login();

        cy.get('a[href="/bolt/content/pages"]').click();
        cy.get('a[href="/bolt/edit/2"]').eq(1).click();

        cy.get('h1').find('span').should('contain', 'Edit Page');
        cy.get('#multiselect-localeswitcher').should('exist');

        cy.get('a[href="/bolt/edit_locales/2"]').click();
        cy.url().should('contain', '/bolt/edit_locales/2');

        cy.get('.table').children('thead').children('tr').children('th').eq(0).should('contain', 'Field');
        cy.get('.table').children('thead').children('tr').children('th').eq(1).should('contain', 'en');
        cy.get('.table').children('thead').children('tr').children('th').eq(2).should('contain', 'nl');
        cy.get('.table').children('thead').children('tr').children('th').eq(3).should('contain', 'ja');
        cy.get('.table').children('thead').children('tr').children('th').eq(4).should('contain', 'nb');

        cy.get('.table').children('tbody').children('tr').eq(0).children('td').eq(0).should('contain', 'Heading');
        cy.get('.table').children('tbody').children('tr').eq(0).children('td').eq(0).should('contain', 'Type: text');
        cy.get('.table').children('tbody').children('tr').eq(0).children('td').eq(1).children('span').should('contain', 'OK');
        cy.get('.table').children('tbody').children('tr').eq(0).children('td').eq(2).children('span').should('contain', 'OK');
        cy.get('.table').children('tbody').children('tr').eq(0).children('td').eq(3).children('span').should('contain', 'OK');
        cy.get('.table').children('tbody').children('tr').eq(0).children('td').eq(4).children('span').should('contain', 'Missing');

        cy.get('.table').children('tbody').children('tr').eq(3).children('td').eq(0).should('contain', 'Eén plaatje');
        cy.get('.table').children('tbody').children('tr').eq(3).children('td').eq(0).should('contain', 'Type: image');
        cy.get('.table').children('tbody').children('tr').eq(3).children('td').eq(1).children('span').should('contain', 'Default');
        cy.get('.table').children('tbody').children('tr').eq(3).children('td').eq(2).children('span').should('contain', 'Default');
        cy.get('.table').children('tbody').children('tr').eq(3).children('td').eq(3).children('span').should('contain', 'Default');
        cy.get('.table').children('tbody').children('tr').eq(3).children('td').eq(4).children('span').should('contain', 'Default');

        cy.get('.table').children('tbody').children('tr').eq(9).children('td').children('a').its('length').should('eq', 4);

        cy.get('.table').children('tbody').children('tr').eq(9).children('td').eq(2).click();
        cy.url().should('contain', '/bolt/edit/2?edit_locale=nl');
    })
});
