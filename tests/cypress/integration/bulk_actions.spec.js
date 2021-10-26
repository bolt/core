/// <reference types="cypress" />

describe('As an admin I want to select all items on a contentlisting page', () => {
    it('checks if an admin can see all items', () => {
        cy.login();
        cy.visit('/bolt/content/pages');
        cy.get("label[for='selectAll']").should('exist');
        cy.get(".listing__filter .custom-checkbox").click();
        
        cy.get('.is-primary').should('contain', '8');
        //cy.get('div[class="card-header"]').should('contain', 'Pages Selected');
        cy.get('.admin__body--aside .card-body .multiselect').should('contain', 'Select option');
        cy.get('button[name="bulk-action"]').should('be.disabled');
    })
});

describe('As an Admin I want to change the status of several tests at once', () => {
    it('checks if an admin can make multiple changes', () => {
        cy.login();
        cy.visit('/bolt/content/tests');
        cy.get(".listing__filter .custom-checkbox").click();
        cy.get(".card-body .multiselect__select").click();
        cy.wait(100);
        
        cy.get('.multiselect--active').should('contain', "Change status to 'publish'");
        cy.get('.multiselect--active').should('contain', "Change status to 'draft'");
        cy.get('.multiselect--active').should('contain', "Change status to 'held'");
        cy.get('.multiselect--active').should('contain', "Delete");

        cy.get("aside .card-body .multiselect__content-wrapper").click();
        cy.get("ul > li:nth-child(2)").click();
        cy.wait(100);
        cy.get('button[name="bulk-action"]').should('be.enabled');
        cy.get('button[name="bulk-action"]').click();
        cy.url().should('contain', '/bolt/content/tests');

        cy.get('.listing__records .is-meta .status.is-draft').its('length').should('eq', 8);
        cy.get('.listing__records .is-meta .status.is-published').should('not.exist');
        cy.get('.listing__records .is-meta .status.is-held').should('not.exist');

        cy.get(".listing__filter .custom-checkbox").click();
        cy.get('.multiselect__select').click();
        cy.get('aside .card-body .multiselect__content-wrapper > ul > li:nth-child(1)').click();
        
        cy.wait(100);
        cy.get('button[name="bulk-action"]').should('be.enabled');
        cy.get('button[name="bulk-action"]').click();
        cy.url().should('contain', '/bolt/content/tests');

        cy.get('.listing__records .is-meta .status.is-published').its('length').should('eq', 8);
        cy.get('.listing__records .is-meta .status-is-draft').should('not.exist');
        cy.get('.listing__records .is-meta .status.is-held').should('not.exist');
        cy.findByText("Status changed successfully");
    })
});
