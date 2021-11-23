// *********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// *********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

import '@testing-library/cypress/add-commands'

Cypress.Commands.add('login', (username = 'admin', password = 'admin%1') => {
    cy.visit('/bolt');

    cy.url().should('include', '/bolt/login');

    cy.get('input[name="login[username]"]').type('{selectall}{backspace}');

    cy.get('input[name="login[username]"]').type(username);
    cy.get('input[name="login[password]"]').type(password + '{enter}');

    cy.url().should('eq', Cypress.config().baseUrl + '/bolt/');
});
