/// <reference types="cypress" />

describe('As an admin I attempt to log in to Dashboard with incorrect credentials', () => {
    it("checks that logging in with incorrect credentials doesn't work", () => {
        
        cy.visit('/bolt/login');

        cy.get('input[name="login[username]"]').type('admin');
        cy.get('input[name="login[password]"]').type('noadmin' + '{enter}');

        cy.url().should('include', '/bolt/login');
        cy.get('div[class="alert alert-danger"]').should('contain', 'Invalid credentials.');
    })
});
