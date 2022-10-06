/// <reference types="cypress" />

describe('As an Admin I want to fill in an imagelist', { retries: 0 }, () => {
    it('checks if an admin can fill in an imagelist', () => {
        cy.login();
        cy.visit('/bolt/edit/42');
        cy.get('a[id="media-tab"]').click({ force: true });
        cy.get("label[for='field-imagelist']").should('contain', 'Imagelist');

        cy.get('.editor__imagelist button[name="image-upload-dropdown"]').eq(1).scrollIntoView();
        cy.get('.editor__imagelist button[name="image-upload-dropdown"]').eq(1).click({ force: true }) ;
        cy.get('.editor__imagelist button[class="btn dropdown-item"]').find('i[class="fas fa-fw fa-th"]').eq(1).click({ force: true }) ;

        // TODO: Re-enable this part, and make it work as expected.

        // cy.get('div[class="modal-dialog"]').its('length').should('eq', 1);
        // cy.get('div[class="modal-dialog"]').find('input[value="kitten2.jpg"]').click({ force: true });
        // cy.get('button[id="modalButtonAccept"]').scrollIntoView().trigger('mouseover', { force: true }).click({ force: true });
        //
        // cy.get('input[name="fields[imagelist][1][filename]"]').should('have.value', 'kitten2.jpg');
        // cy.get('input[name="fields[imagelist][1][alt]"]').clear({ force: true });
        // cy.get('input[name="fields[imagelist][1][alt]"]').type('Image of a kitten', { force: true });
        //
        // cy.get('button[class="btn btn-tertiary"]').eq(0).click({ force: true}) ;
        // cy.get('.editor__imagelist').find('div[class="form-fieldsgroup"]').its('length').should('eq', 5);
        //
        // cy.get('button[name="image-upload-dropdown"]').eq(5).scrollIntoView();
        // cy.get('button[name="image-upload-dropdown"]').eq(5).click({ force: true}) ;
        // cy.get('button[class="btn dropdown-item"]').find('i[class="fas fa-fw fa-th"]').eq(5).click({ force: true}) ;
        //
        // cy.get('div[class="modal-dialog"]').its('length').should('eq', 1);
        // cy.get('div[class="modal-dialog"]').find('input[value="joey.jpg"]').click({ force: true });
        // cy.get('button[id="modalButtonAccept"]').scrollIntoView().trigger('mouseover', { force: true }).click({ force: true });
        //
        // cy.get('input[name="fields[imagelist][3][filename]"]').should('have.value', 'joey.jpg');
        // cy.get('input[name="fields[imagelist][3][alt]"]').clear({ force: true });
        // cy.get('input[name="fields[imagelist][3][alt]"]').type('Image of a joey', { force: true });
        //
        // cy.get('button[class="btn btn-sm btn-tertiary"]').find('i[class="fas fa-fw fa-chevron-down"]').eq(0).scrollIntoView();
        // cy.get('button[class="btn btn-sm btn-tertiary"]').find('i[class="fas fa-fw fa-chevron-down"]').eq(0).click({ force: true}) ;


        // cy.get('input[name="fields[imagelist][1][filename]"]').should('have.value', 'kitten2.jpg');
        // cy.get('input[name="fields[imagelist][1][alt]"]').should('have.value', 'Image of a kitten');
        // cy.wait(2000);
        //
        // cy.get('button[class="btn btn-sm btn-tertiary"]').find('i[class="fas fa-fw fa-chevron-up"]').eq(1).scrollIntoView();
        // cy.get('button[class="btn btn-sm btn-tertiary"]').find('i[class="fas fa-fw fa-chevron-up"]').eq(1).click({ force: true}) ;
        // cy.get('input[name="fields[imagelist][0][filename]"]').should('have.value', 'kitten2.jpg');
        // cy.get('input[name="fields[imagelist][0][alt]"]').should('have.value', 'Image of a kitten');
        //
        // cy.get('div[class="btn-group me-2"]').eq(5).find('button[disabled="disabled"]');
        // cy.get('div[class="btn-group me-2"]').eq(13).find('button[disabled="disabled"]');
        //
        // cy.get('.form-fieldsgroup:nth-child(1) > .editor__image .btn:nth-child(3)').click({ force: true}) ;
        // cy.get('button[class="btn btn-success mb-0"]').eq(1).click({ force: true}) ;
        // //TODO: move checking for elements before saving changes(for some reason it doesn't work)

        cy.get('.editor__imagelist').find('div[class="form-fieldsgroup"]').its('length').should('eq', 4);
        cy.url().should('contain', '/bolt/edit/42');
    });
});
