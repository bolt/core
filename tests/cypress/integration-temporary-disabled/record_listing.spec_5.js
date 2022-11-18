/// <reference types="cypress" />

describe('As an Admin I want to see the last edited records in the sidebar', () => {
    it('checks that an admin can see the last edited records in the sidebar', () => {
        cy.login();
        cy.visit('/bolt/edit/74');
        cy.get('button[name="save"]').eq(1).scrollIntoView();
        cy.get('button[name="save"]').eq(1).click();

        cy.get('a[href="/bolt/content/tests"]').trigger('mouseover', { force: true });
        cy.get('ul[class="admin__sidebar--menu"]').find('li').eq(3).find('a').find('ul[class="link--menu"]').find('li').its('length').should('eq', 6);
        cy.get('#bolt--sidebar ul li:nth-child(8) ul > li:nth-child(1) > a').find('span').should('contain', 'New');
        cy.get('#bolt--sidebar ul li:nth-child(8) ul > li:nth-child(2) > a').find('span').should('contain', '74: Title of the test');
    })
});
