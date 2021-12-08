/// <reference types="cypress" />

describe('As a user I want to see the results of Setcontent', () => {
    it('checks that the Setcontent page is visible as a user', () => {
        cy.visit('/page/setcontent-test-page');
        cy.get('#results-one').should('contain', 'yes');
        cy.get('#results-two').should('contain', 'yes');
        cy.get('#results-four').should('contain', 'yes');
        cy.get('#results-five').should('contain', 'yes');
        cy.get('span#results-six').should('not.contain', 'no');

        cy.get('#three .s1').should('contain', 2);
        cy.get('#three .s2').should('contain', 'published');

        cy.get('main').should('not.contain', '[no]');
    })

    it('checks that the Setcontent order by taxonomy sortorder', () => {
        cy.visit('/page/setcontent-test-page');
        cy.get('#results-fourteen').should('contain', 'yes');
        cy.get('#results-fifteen').should('contain', 'yes');
    })

    it('checks that the Setcontent is visible on a translated page as a user', () => {
        cy.visit('/nl/page/setcontent-test-page');
        cy.get('#results-one').should('contain', 'yes');
        cy.get('#results-two').should('contain', 'yes');
        cy.get('#results-four').should('contain', 'yes');
        cy.get('#results-five').should('contain', 'yes');
        cy.get('span#results-six').should('not.contain', 'no');

        cy.get('#three .s1').should('contain', 2);
        cy.get('#three .s2').should('contain', 'published');

        cy.get('main').should('not.contain', '[no]');
    })
});
