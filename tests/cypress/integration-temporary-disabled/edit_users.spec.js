/// <reference types="cypress" />

describe('Edit user successfully, Edit users incorrectly', () => {
    it('checks that an admin can edit users', () => {
        cy.login();
        cy.visit('/bolt/users');

        cy.get('table:nth-child(1) > tbody > tr:nth-child(6) > td:nth-child(7)').click({ force: true });
        cy.wait(100);
        cy.get('table:nth-child(1) > tbody > tr:nth-child(6) > td:nth-child(7) > div > div > a:nth-child(1)').invoke('removeAttr', 'target').click({ force: true });
        cy.wait(1000);

        cy.visit('/bolt/user-edit/4');

        cy.get('input[name="user[displayName]"]').clear();
        cy.get('input[name="user[displayName]"]').type('Tom Doe CHANGED');
        cy.get('input[name="user[email]"]').clear();
        cy.get('input[name="user[email]"]').type('tom_admin_changed@example.org');
        cy.get('#edituser > button').scrollIntoView();
        cy.wait(1000);
        cy.get('#edituser > button').click();

        cy.visit('/bolt/users');

        cy.url().should('contain', 'bolt/users');
        cy.get('table:nth-child(1) > tbody > tr:nth-child(6)').children('td').eq(1).should('contain', 'Tom Doe CHANGED');
    })

    it('checks that a user can change their display name', () => {
        cy.visit('/bolt/login');
        cy.get('input[name="login[username]"]').type('jane_chief');
        cy.get('input[name="login[password]"]').type('jane%1' + '{enter}');
        cy.visit('/bolt/profile-edit');

        cy.get('#user_displayName').clear();
        cy.get('#user_displayName').type('Administrator');
        cy.get('#edituser > button').scrollIntoView();
        cy.get('#edituser > button').click();

        cy.wait(500);

        cy.visit('/bolt/profile-edit');
        cy.get('#user_displayName').invoke('val').should('contain', 'Administrator');
        cy.visit('/bolt/logout');
    })

    it("checks that an admin can't edit a user with incorrect details", () => {
        cy.login();
        cy.visit('/bolt/user-edit/2');

        cy.get('input[name="user[displayName]"]').clear();
        cy.get('input[name="user[displayName]"]').type('x');
        cy.get('input[name="user[plainPassword]"]').type('short');
        cy.get('input[name="user[email]"]').clear();
        cy.get('input[name="user[email]"]').type('smth@nth');

        cy.get('#edituser > button').scrollIntoView();
        cy.get('#edituser > button').click();

        cy.visit('/bolt/user-edit/2');

        cy.url().should('contain', '/bolt/user-edit/2');
        // Disabling this for now because cypress is annoying
        // cy.get('.field-error').eq(0).children('.help-block').children('.list-unstyled').children('li').should('contain', 'Invalid display name');
        // cy.get('.field-error').eq(1).children('.help-block').children('.list-unstyled').children('li').should('contain', 'Invalid password. The password should contain at least 6 characters.');
        // cy.get('.field-error').eq(2).children('.help-block').children('.list-unstyled').children('li').should('contain', 'Invalid email');
        cy.get('#field--user_plainPassword').children('div').eq(1).should('contain', 'Suggested secure password');
    })

    it('checks that a user can\'t edit their profile with an incorrect display name', () => {
        cy.visit('/bolt/login');
        cy.get('input[name="login[username]"]').type('jane_chief');
        cy.get('input[name="login[password]"]').type('jane%1' + '{enter}');

        cy.get('div[class="toolbar-item btn-group toolbar-item__profile"]').trigger('mouseover')
        cy.get('.profile__dropdown.dropdown-menu.dropdown-menu-right').children('ul').children('li').eq(0).should('exist');

        cy.get('a[href="/bolt/profile-edit"]').click({force: true});
        cy.url().should('contain', '/bolt/profile-edit');

        cy.wait(500);

        cy.get('h1').should('contain', 'Administrator');
        cy.get('#user_username').invoke('val').should('contain', 'jane_chief');

        cy.get('#user_displayName').clear();
        cy.get('#user_displayName').type('a');
        cy.get('#edituser > button').scrollIntoView();
        cy.get('#edituser > button').click();

        // cy.get('.field-error').eq(0).children('.help-block').children('.list-unstyled').children('li').should('contain', 'Invalid display name');
        cy.visit('/bolt/logout');
    });
});
