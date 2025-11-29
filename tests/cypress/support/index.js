import '@testing-library/cypress/add-commands';

Cypress.Commands.add('login', (username = 'admin', password = 'admin%1', checkPath = true) => {
    cy.visit('/bolt');

    cy.url().should('include', '/bolt/login');

    cy.get('input[name="login[username]"]').type('{selectall}{backspace}');

    cy.get('input[name="login[username]"]').type(username);
    cy.get('input[name="login[password]"]').type(password + '{enter}');

    if (checkPath) {
        cy.url().should('eq', Cypress.config().baseUrl + '/bolt/');
    }
});
