/// <reference types="cypress" />

describe('As an Admin I want to see the settings menu items', () => {
    it('checks that an admin can see the settings menu items', () => {
        cy.login();

        cy.get('.admin__sidebar--menu').should('contain', 'Configuration');

        cy.get('#bolt--sidebar ul > li:nth-child(12) > a').trigger('mouseover');
        cy.get('#bolt--sidebar ul > li:nth-child(12) li:nth-child(1)').find('span').should('contain', 'View Configuration');
        cy.get('#bolt--sidebar ul li:nth-child(12) ul > li:nth-child(2) > a').find('span').should('contain', 'Users & Permissions');
        cy.get('#bolt--sidebar ul li:nth-child(12) ul > li:nth-child(3) > a').find('span').should('contain', 'Main Configuration');
        cy.get('#bolt--sidebar ul li:nth-child(12) ul > li:nth-child(4) > a').find('span').should('contain', 'Content Types');
        cy.get('#bolt--sidebar ul li:nth-child(12) ul > li:nth-child(5) > a').find('span').should('contain', 'Taxonomies');
        cy.get('#bolt--sidebar ul li:nth-child(12) ul > li:nth-child(6) > a').find('span').should('contain', 'Menu set up');
        cy.get('#bolt--sidebar ul li:nth-child(12) ul > li:nth-child(7) > a').find('span').should('contain', 'Routing configuration');
        cy.get('#bolt--sidebar ul li:nth-child(12) ul > li:nth-child(8) > a').find('span').should('contain', 'All configuration files');
    })
});
