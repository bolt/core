/// <reference types="cypress" />

describe('Disable/enable users', () => {
    it('checks that an admin can disable/enable another user', () => {
        cy.login('jane_chief', 'jane%1');
        cy.get('h1')
            .find('span')
            .should('contain', 'Bolt Dashboard');

        cy.clearCookies();
        cy.login();
        cy.visit('bolt/users');

        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .scrollIntoView();
        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .click({ force: true });
        cy.wait(100);
        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .find('a')
            .eq(1)
            .click({ force: true });
        cy.wait(1000);

        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .click({ force: true });
        cy.wait(100);
        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .find('a')
            .eq(1)
            .should('contain', 'Enable');

        cy.clearCookies();
        cy.login('jane_chief', 'jane%1', false);
        cy.get('div[class="alert alert-danger"]').should('contain', 'User is disabled');

        cy.clearCookies();
        cy.login();
        cy.visit('bolt/users');
        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .scrollIntoView();
        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .click({ force: true });
        cy.wait(100);

        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .find('a')
            .eq(1)
            .should('contain', 'Enable');
        cy.get('table')
            .eq(0)
            .find('tbody')
            .find('tr')
            .eq(2)
            .find('td')
            .eq(5)
            .find('a')
            .eq(1)
            .click({ force: true });
        cy.wait(100);

        cy.clearCookies();
        cy.login('jane_chief', 'jane%1');
        cy.get('.admin__header--title__prefix').should('contain', 'Bolt Dashboard');
        cy.visit('bolt/logout');
    });
});
