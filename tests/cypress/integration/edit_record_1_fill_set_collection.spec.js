/// <reference types="cypress" />

describe('As an Admin I want to fill in a Set and an Collection', () => {
    it('checks if an admin can fill in a set', () => {
        cy.login();
        cy.visit('/bolt/edit/43');
        cy.get('.editor__tabbar').should('contain', 'Sets');

        cy.get('h1.admin__header--title').scrollIntoView();

        cy.get('a[id="sets-tab"]').click();
        cy.url().should('contain', '/bolt/edit/43');

        cy.get("#sets label[for='field-set-title']").should('contain', 'Title');
        cy.get('input[name="sets[set][title]"]').its('length').should('eq', 1);
        cy.get("#sets label[for='field-set-textarea']").should('contain', 'Textarea');
        cy.get('textarea[name="sets[set][textarea]"]').its('length').should('eq', 1);

        cy.get('input[name="sets[set][title]"]').clear();
        cy.get('input[name="sets[set][title]"]').type('Foo');
        cy.get('textarea[name="sets[set][textarea]"]').clear();
        cy.get('textarea[name="sets[set][textarea]"]').type('Bar');

        cy.get('button[class="btn btn-success mb-0"]').eq(1).scrollIntoView();
        cy.get('button[class="btn btn-success mb-0"]').eq(1).click();

        cy.url().should('contain', '/bolt/edit/43');
        cy.get('input[name="sets[set][title]"]').should('have.value', 'Foo');
        cy.get('textarea[name="sets[set][textarea]"]').should('have.value', 'Bar');
    })

    it('checks if an admin can fill in a collection', () => {
        cy.login();
        cy.visit('/bolt/edit/43');
        cy.get('.editor__tabbar').should('contain', 'Collections');

        cy.get('a[id="collections-tab"]').click();
        cy.url().should('contain', '/bolt/edit/43');
        cy.get("label[for='field-collection']").should('contain', "Collection:");

        cy.get('button[id="collection-dropdownMenuButton"]').scrollIntoView();
        cy.get('button[id="collection-dropdownMenuButton"]').click();
        cy.get("#field--field-collection > div > div > div > div.dropdown > div > a:nth-child(2)").click();

        cy.get('.action-move-up-collection-item').eq(0).should('be.disabled');
        cy.get('.action-move-down-collection-item').should('be.enabled');

        cy.get('button[id="collection-dropdownMenuButton"]').scrollIntoView();
        cy.get('button[id="collection-dropdownMenuButton"]').click();
        cy.get("#field--field-collection > div > div > div > div.dropdown > div > a:nth-child(1)").click();

        cy.get('.collection-item').its('length').should('eq', 4);
        cy.get('#collections textarea').eq(1).clear();
        cy.get('#collections textarea').eq(1).type('Bye, Bolt');
        cy.get(".collection-item input[type='text']").eq(1).clear();
        cy.get(".collection-item input[type='text']").eq(1).type('Hey, Bolt');

        cy.get('.collection-item:nth-child(4) .action-move-down-collection-item').scrollIntoView();
        cy.get('.collection-item:nth-child(4) .action-move-down-collection-item').click();
        cy.get('div[data-label="Set inside Collection"]').should('exist');

        cy.get('button[class="btn btn-success mb-0"]').eq(1).scrollIntoView();
        cy.get('button[class="btn btn-success mb-0"]').eq(1).click({ force: true });
        cy.url().should('contain', '/bolt/edit/43');

        cy.get('#collections textarea').eq(1).clear();
        cy.get(".collection-item input[type='text']").eq(1).clear();

        //TODO: Find out where the additional first element comes from within CSS
        cy.get('.collection-item:nth-child(4) .collection-item-title').should('contain', 'Set');
        cy.get('.collection-item:nth-child(5) .collection-item-title').should('contain', 'Textarea');

        cy.get('.collection-item:nth-child(5) .action-remove-collection-item').scrollIntoView();
        cy.get('.collection-item:nth-child(5) .action-remove-collection-item').click({force: true});

        cy.wait(1000);

        cy.get('.collection-item:nth-child(4) .action-remove-collection-item').scrollIntoView();
        cy.get('.collection-item:nth-child(4) .action-remove-collection-item').click({force: true});

        cy.wait(1000);

        cy.get('.collection-item').its('length').should('eq', 2);
        cy.get('button[class="btn btn-success mb-0"]').eq(1).scrollIntoView();
        cy.get('button[class="btn btn-success mb-0"]').eq(1).click({force: true});

        cy.get('.collection-item').its('length').should('eq', 2);
        cy.get('.collection-item-title').should('not.contain', 'Hey, Bolt');
        cy.get('.collection-item-title').should('not.contain', 'Bye, Bolt');
    });
});
