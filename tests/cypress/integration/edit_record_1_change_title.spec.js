/// <reference types="cypress" />

describe('As an Admin I want to change the title and the locale of a record', () => {
    it("checks if an admin can change a record's title", () => {
        cy.login();
        cy.visit('/bolt/edit/30');
        cy.get('input[id="field-title"]').clear();
        cy.get('input[id="field-title"]').type('Changed title');

        cy.get('button[name="save"]').eq(1).scrollIntoView();
        cy.get('button[name="save"]').eq(1).click();

        cy.visit('/bolt/edit/30');
        cy.reload();
        cy.wait(1000);
        cy.get('input[id="field-title"]').should('have.value', 'Changed title');
    })

    it("checks if an admin can change a record's title in another language", () => {
        cy.login();
        cy.visit('/bolt/edit/1');
        cy.get('input[id="field-title"]').clear();
        cy.get('input[id="field-title"]').type('Changed title EN');

        cy.get('button[name="save"]').eq(1).scrollIntoView();
        cy.get('button[name="save"]').eq(1).click();

        cy.get('#multiselect-localeswitcher div.multiselect__select').scrollIntoView();
        cy.get('#multiselect-localeswitcher div.multiselect__content-wrapper > ul > li:nth-child(2) > span').click({force: true});

        cy.get('input[id="field-title"]').clear();
        cy.get('input[id="field-title"]').type('Changed title NL');

        cy.get('button[name="save"]').eq(1).scrollIntoView();
        cy.get('button[name="save"]').eq(1).click();

        cy.visit('/bolt/edit/1?edit_locale=nl');
        cy.get('.admin__header--title').its('length').should('eq', 1);
        cy.get('input[id="field-title"]').should('have.value', 'Changed title NL');

        cy.visit('/bolt/edit/1?edit_locale=en&_locale=nl');
        cy.get('.admin__header--title').its('length').should('eq', 1);
        cy.get('input[id="field-title"]').should('have.value', 'Changed title EN');

        cy.visit('/page/1?_locale=nl');
        cy.findByText('Changed title NL');
    })
});
