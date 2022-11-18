/// <reference types="cypress" />

describe('As an Admin I want to use record listing', () => {
    it('checks that an admin can navigate over the record listing', () => {
        cy.login();
        cy.get('a[rel=next]').scrollIntoView();
        cy.get('a[rel=next]').click();
        cy.get('#listing .listing__row .is-details a').should('exist');
    })
});
