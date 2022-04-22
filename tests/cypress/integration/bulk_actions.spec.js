/// <reference types="cypress" />

describe('As an admin I should be able to run bulk actions', () => {
    it('checks if an admin can see all items', () => {
        cy.login();
        cy.visit('/bolt/content/pages');
        cy.get("label[for='selectAll']").should('exist');
        cy.get(".listing__filter input[type='checkbox']").click({ force: true });

        cy.get('.is-primary').should('contain', '8');
        //cy.get('div[class="card-header"]').should('contain', 'Pages Selected');
        cy.get('.admin__body--aside .card-body .multiselect').should('contain', 'Select option');
        cy.get('button[name="bulk-action"]').should('be.disabled');
    })

    it('checks if an admin can make multiple changes at once', () => {
        cy.login();
        cy.visit('/bolt/content/tests');
        cy.get(".listing__filter input[type='checkbox']").click({ force: true });
        cy.get(".card-body .multiselect__select").click({ force: true });
        cy.wait(100);

        cy.get('.multiselect--active').should('contain', "Change status to 'publish'");
        cy.get('.multiselect--active').should('contain', "Change status to 'draft'");
        cy.get('.multiselect--active').should('contain', "Change status to 'held'");
        cy.get('.multiselect--active').should('contain', "Delete");

        cy.get('aside .card-body .multiselect__content-wrapper > ul > li:nth-child(2)').click();
        cy.wait(100);
        cy.get('button[name="bulk-action"]').should('be.enabled');
        cy.get('button[name="bulk-action"]').click();
        cy.url().should('contain', '/bolt/content/tests');

        // TODO Disable server cache for bulk actions
        cy.visit('/bolt/clearcache');
        cy.wait(1000);

        cy.visit('/bolt/content/tests');

        cy.get('.listing__records .is-meta .status.is-draft').its('length').should('eq', 8);
        cy.get('.listing__records .is-meta .status.is-published').should('not.exist');
        cy.get('.listing__records .is-meta .status.is-held').should('not.exist');

        cy.get(".listing__filter input[type='checkbox']").click({ force: true });
        cy.get('.multiselect__select').click({ force: true });
        cy.get('aside .card-body .multiselect__content-wrapper > ul > li:nth-child(1)').click();

        cy.wait(100);
        cy.get('button[name="bulk-action"]').should('be.enabled');
        cy.get('button[name="bulk-action"]').click({ force: true });
        cy.url().should('contain', '/bolt/content/tests');

        // TODO Disable server cache for bulk actions
        cy.visit('/bolt/clearcache');
        cy.wait(1000);

        cy.visit('/bolt/content/tests');

        cy.get('.listing__records .is-meta .status.is-published').its('length').should('eq', 8);
        cy.get('.listing__records .is-meta .status-is-draft').should('not.exist');
        cy.get('.listing__records .is-meta .status.is-held').should('not.exist');
    })
});
