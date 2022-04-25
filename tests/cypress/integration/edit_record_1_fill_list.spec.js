/// <reference types="cypress" />

describe('As an Admin I want to fill in an imagelist and filelist', () => {
    it('checks if an admin can fill in an imagelist', () => {
        cy.login();
        cy.visit('/bolt/edit/42');
        cy.get('a[id="media-tab"]').click({ force: true }) ;
        cy.get("label[for='field-imagelist']").should('contain', 'Imagelist');

        cy.get('button[name="image-upload-dropdown"]').eq(1).scrollIntoView();
        cy.get('button[name="image-upload-dropdown"]').eq(1).click({ force: true }) ;
        cy.get('button[class="btn dropdown-item"]').find('i[class="fas fa-fw fa-th"]').eq(1).click({ force: true }) ;

        cy.get('div[class="modal-dialog"]').its('length').should('eq', 1);
        cy.get('div[class="modal-dialog"]').find('input[value="kitten2.jpg"]').click({ force: true });
        cy.get('button[id="modalButtonAccept"]').trigger('mouseover', { force: true }).scrollIntoView().click({ force: true });
        cy.wait(1000);

        cy.get('input[name="fields[imagelist][0][filename]"]').should('have.value', 'kitten2.jpg');
        cy.get('input[name="fields[imagelist][0][alt]"]').clear();
        cy.get('input[name="fields[imagelist][0][alt]"]').type('Image of a kitten');

        cy.get('button[class="btn btn-tertiary"]').eq(0).click({ force: true}) ;
        cy.get('.editor__imagelist').find('div[class="form-fieldsgroup"]').its('length').should('eq', 5);

        cy.get('button[name="image-upload-dropdown"]').eq(5).scrollIntoView();
        cy.get('button[name="image-upload-dropdown"]').eq(5).click({ force: true}) ;
        cy.get('button[class="btn dropdown-item"]').find('i[class="fas fa-fw fa-th"]').eq(5).click({ force: true}) ;

        cy.get('div[class="modal-dialog"]').its('length').should('eq', 1);
        cy.get('div[class="modal-dialog"]').find('input[value="joey.jpg"]').click({ force: true });
        cy.get('button[id="modalButtonAccept"]').trigger('mouseover', { force: true }).scrollIntoView().click({ force: true });
        cy.wait(1000);

        cy.get('input[name="fields[imagelist][4][filename]"]').should('have.value', 'joey.jpg');
        cy.get('input[name="fields[imagelist][4][alt]"]').clear();
        cy.get('input[name="fields[imagelist][4][alt]"]').type('Image of a joey');

        cy.get('button[class="btn btn-sm btn-tertiary"]').find('i[class="fas fa-fw fa-chevron-down"]').eq(0).scrollIntoView();
        cy.get('button[class="btn btn-sm btn-tertiary"]').find('i[class="fas fa-fw fa-chevron-down"]').eq(0).click({ force: true}) ;
        cy.get('input[name="fields[imagelist][1][filename]"]').should('have.value', 'kitten.jpg');
        cy.get('input[name="fields[imagelist][1][alt]"]').should('have.value', 'Image of a kitten');

        cy.get('button[class="btn btn-sm btn-tertiary"]').find('i[class="fas fa-fw fa-chevron-up"]').eq(1).scrollIntoView();
        cy.get('button[class="btn btn-sm btn-tertiary"]').find('i[class="fas fa-fw fa-chevron-up"]').eq(1).click({ force: true}) ;
        cy.get('input[name="fields[imagelist][0][filename]"]').should('have.value', 'kitten.jpg');
        cy.get('input[name="fields[imagelist][0][alt]"]').should('have.value', 'Image of a kitten');

        cy.get('div[class="btn-group mr-2"]').eq(3).find('button[disabled="disabled"]');
        cy.get('div[class="btn-group mr-2"]').eq(11).find('button[disabled="disabled"]');

        cy.get('.form-fieldsgroup:nth-child(1) > .editor__image .btn:nth-child(3)').click({ force: true}) ;
        cy.get('button[class="btn btn-success mb-0 "]').eq(1).click({ force: true}) ;
        //TODO: move checking for elements before saving changes(for some reason it doesn't work)
        cy.get('.editor__imagelist').find('div[class="form-fieldsgroup"]').its('length').should('eq', 5);
        cy.url().should('contain', '/bolt/edit/42#media');
    })

    it('checks if an admin can fill in an filelist', () => {
        cy.login();
        cy.visit('/bolt/edit/42');
        cy.get('a[id="files-tab"]').click({ force: true}) ;
        cy.get("label[for='field-filelist']").should('contain', 'Filelist');

        cy.get('button[name="file-upload-dropdown"]').eq(1).scrollIntoView();
        cy.get('button[name="file-upload-dropdown"]').eq(1).click({ force: true}) ;
        cy.get('button[class="btn dropdown-item"]').find('i[class="fas fa-fw fa-th"]').eq(6).click({ force: true}) ;

        cy.get('div[class="modal-dialog"]').its('length').should('eq', 1);
        cy.get('div[class="modal-dialog"]').find('input[value="bolt4.pdf"]').click({ force: true });
        cy.get('button[id="modalButtonAccept"]').trigger('mouseover', { force: true }).scrollIntoView().click({ force: true });
        cy.wait(1000);

        cy.get('input[name="fields[filelist][0][filename]"]').should('have.value', 'bolt4.pdf');

        cy.get('button[class="btn btn-tertiary"]').eq(1).click({ force: true}) ;
        cy.get('.editor-filelist').find('div[class="form-fieldsgroup"]').its('length').should('eq', 5);
        cy.get('button[class="btn btn-tertiary"]').eq(1).should('be.disabled');

        cy.get('button[name="file-upload-dropdown"]').eq(5).scrollIntoView();
        cy.get('button[name="file-upload-dropdown"]').eq(5).click({ force: true}) ;
        cy.get('button[class="btn dropdown-item"]').find('i[class="fas fa-fw fa-th"]').eq(10).click({ force: true}) ;

        cy.get('div[class="modal-dialog"]').its('length').should('eq', 1);
        cy.get('div[class="modal-dialog"]').find('input[value="joey.jpg"]').click({ force: true });
        cy.get('button[id="modalButtonAccept"]').trigger('mouseover', { force: true }).scrollIntoView().click({ force: true });
        cy.wait(1000);

        cy.get('input[name="fields[filelist][4][filename]"]').should('have.value', 'joey.jpg');

        cy.get('.form-fieldsgroup:nth-child(1) > .editor__file .btn-group:nth-child(2) > .btn:nth-child(2)').scrollIntoView();
        cy.get('.form-fieldsgroup:nth-child(1) > .editor__file .btn-group:nth-child(2) > .btn:nth-child(2)').click({ force: true}) ;
        cy.get('input[name="fields[filelist][1][filename]"]').should('have.value', 'bolt4.pdf');

        cy.get('.form-fieldsgroup:nth-child(2) > .editor__file .btn-group:nth-child(2) > .btn:nth-child(1) > .fas').click({ force: true}) ;
        cy.get('input[name="fields[filelist][0][filename]"]').should('have.value', 'bolt4.pdf');

        cy.get('div[class="btn-group mr-2"]').eq(13).find('button[disabled="disabled"]');
        cy.get('div[class="btn-group mr-2"]').eq(21).find('button[disabled="disabled"]');

        cy.get('.form-fieldsgroup:nth-child(1) > .editor__file .btn-hidden-danger').click({ force: true}) ;
        cy.get('button[class="btn btn-tertiary"]').eq(0).should('be.enabled');
        cy.get('button[class="btn btn-success mb-0 "]').eq(1).click({ force: true}) ;
        //TODO: move checking for elements before saving changes(for some reason it doesn't work)
        cy.get('.editor-filelist').find('div[class="form-fieldsgroup"]').its('length').should('eq', 4);
        cy.url().should('contain', '/bolt/edit/42#files');

    })
});
