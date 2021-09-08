/// <reference types="cypress" />


describe('As a user I want to see how the record title is displayed', () => {
    it('checks that the record title is displayed as a user', () => {
        cy.visit('/test/title-of-the-test');
        cy.get('.title').should('have.length', 1);
        cy.get('.title').should('contain', '74: Title of the test');
    })
});

describe('As a user I want to see how fields are escaped', () => {
    it('checks that fields are escaped as a user', () => {
        cy.visit('/test/title-of-the-test');
        cy.get('.title').should('have.length', 1);

        cy.get('.text_markup_a').should('contain', 'Text with markup allowed');
        cy.get('.text_markup_b').should('contain', 'Text with markup allowed');
        cy.get('.text_markup_c').should('contain', 'Text with <em>markup allowed</em>.');

        cy.get('.text_plain_a').should('contain', 'Text with <strong>no</strong> markup allowed');
        cy.get('.text_plain_b').should('contain', 'Text with no markup allowed');
        cy.get('.text_plain_c').should('contain', 'Text with <strong>no</strong> markup allowed');

        cy.get('.text_html').should('contain', 'HTML field with simple HTML in it.');
        cy.get('.text_markdown').should('have.text', 'Markdown field with simple Markdown in it.');
        cy.get('.text_textarea').should('have.text', 'Textarea field with simple HTML in it.');

        cy.get('div.box.text_sanitise_a').should('not.contain', 'Text field with <strong>markup</strong>, including . The end.');
        cy.get('.box text_sanitise_b').should('contain', 'Text field with <strong>markup</strong>, including . The end.');
    })
});

describe('As a user I want to see how file fields are displayed', () => {
    it('checks that file fields are displayed as a user', () => {
        cy.visit('/test/title-of-the-test');
        cy.get('.title').should('have.length', 1);
        cy.get('#attachment #filename').should('contain', 'joey.jpg');
        cy.get('#attachment #path').should('contain', '/files/joey.jpg');
        cy.get('#attachment #fieldname').should('contain', 'attachment');
        cy.get('#attachment #url').should('contain', 'http');
        cy.get('#attachment #url').should('contain', '://');
        cy.get('#attachment #url').should('contain', '/files/joey.jpg');
    })
});
