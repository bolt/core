/// <reference types="cypress" />

describe('As an Admin I want to navigate over record listing', () => {
    it('checks that an admin can navigate over the record listing', () => {
        cy.login();
        cy.get('a[rel=next]').scrollIntoView();
        cy.get('a[rel=next]').click();
        cy.get('#listing .listing__row .is-details a').should('exist');
    })
});

describe('As an Admin I want to sort content', () => {
    it('checks that an admin can sort content', () => {
        cy.login();
        cy.visit('/bolt');

        cy.findAllByText('Entries').click();
        cy.url().should('contain', '/bolt/content/entries');
        cy.get('.admin__header--title').should('contain', 'Entries');
        cy.get('div[class="card-header"]').should('contain', 'Contentlisting');

        cy.get('select[name="sortBy"]').select('author');
        cy.get('button[class="btn btn-secondary mb-0 "]').should('contain', 'Filter').click();

        cy.url().should('contain', '/bolt/content/entries?sortBy=author&filter=');
        cy.get('.listing__row--list').eq(0).find('li').eq(1).should('contain', 'Admin');
        cy.get('.listing__row--list').eq(7).find('li').eq(1).should('not.contain', 'Admin');
    })
});

describe('As an Admin I want to filter content', () => {
    it('checks that an admin can filter content', () => {
        cy.login();
        cy.visit('/bolt/content/entries');

        cy.get('div[class="card-header"]').should('contain', 'Contentlisting');

        cy.get('#content-filter').type('a');
        cy.get('button[class="btn btn-secondary mb-0 "]').should('contain', 'Filter').click();

        cy.url().should('contain', '/bolt/content/entries?sortBy=&filter=a');
        cy.get('.listing--container').its('length').should('eq', 10);

        cy.wait(1000);

        cy.get('#content-filter').clear();
        cy.get('#content-filter').type('Entries');
        cy.get('button[class="btn btn-secondary mb-0 "]').should('contain', 'Filter').click();
        cy.url().should('contain', '/bolt/content/entries?sortBy=&filter=Entries');
        cy.get('.listing--container').find('div[class="listing__row is-normal"]').find('div[class="listing__row--item is-details"]').find('a').should('contain', 'Entries');

        cy.wait(1000);

        cy.get('#content-filter').clear();
        cy.get('#content-filter').type(' ');
        cy.get('button[class="btn btn-secondary mb-0 "]').should('contain', 'Filter').click();
        cy.url().should('contain', '/bolt/content/entries?sortBy=&filter=');
        cy.get('.listing--container').its('length').should('eq', 10);
    })
});

describe('As a user I want to see contenttype listing', () => {
    it('checks that a user can see the contenttype listing', () => {
        cy.visit('/pages');
        cy.get('article').its('length').should('eq', 6);
    })
});

describe('As an admin I want to see expanded and compact contenttype listing', () => {
    it('checks that an admin can expand and compact the contenttype listing', () => {
        cy.login();
        cy.visit('/bolt/content/pages');
        cy.get('button[title="Expanded"]').should('exist');
        cy.get('button[title="Compact"]').should('exist');

        cy.get('button[title="Compact"]').click();
        cy.get('div[class="listing__row--item is-thumbnail"]').should('not.exist');
        cy.get('span[class="listing__row--item-title-excerpt"]').should('not.be.visible');
        cy.wait(3000);

        cy.get('button[title="Expanded"]').click();
        cy.get('div[class="listing__row--item is-thumbnail"]').should('be.visible');
        cy.get('span[class="listing__row--item-title-excerpt"]').should('be.visible');
    })
});

describe('As an admin I want to see the the last edited records in the sidebar', () => {
    it('checks that an admin can see the last edited records in the sidebar', () => {
        cy.login();
        cy.visit('/bolt/edit/74');
        cy.get('button[name="save"]').eq(1).scrollIntoView();
        cy.get('button[name="save"]').eq(1).click();

        cy.get('a[href="/bolt/content/tests"]').trigger('mouseover');
        cy.get('ul[class="admin__sidebar--menu"]').find('li').eq(3).find('a').find('ul[class="link--menu"]').find('li').its('length').should('eq', 6);
        cy.get('#bolt--sidebar ul li:nth-child(8) ul > li:nth-child(1) > a').find('span').should('contain', 'New');
        cy.get('#bolt--sidebar ul li:nth-child(8) ul > li:nth-child(2) > a').find('span').should('contain', 'Title of the test');
    })
});

describe('As an admin I want to see the settings menu items', () => {
    it('checks that an admin can see the settings menu items', () => {
        cy.login();

        cy.get('.admin__sidebar--menu').should('contain', 'Configuration');

        cy.get('#bolt--sidebar ul > li:nth-child(11) > a').trigger('mouseover');
        cy.get('#bolt--sidebar ul > li:nth-child(11) li:nth-child(1)').find('span').should('contain', 'View Configuration');
        cy.get('#bolt--sidebar ul li:nth-child(11) ul > li:nth-child(2) > a').find('span').should('contain', 'Users & Permissions');
        cy.get('#bolt--sidebar ul li:nth-child(11) ul > li:nth-child(3) > a').find('span').should('contain', 'Main Configuration');
        cy.get('#bolt--sidebar ul li:nth-child(11) ul > li:nth-child(4) > a').find('span').should('contain', 'Content Types');
        cy.get('#bolt--sidebar ul li:nth-child(11) ul > li:nth-child(5) > a').find('span').should('contain', 'Taxonomies');
        cy.get('#bolt--sidebar ul li:nth-child(11) ul > li:nth-child(6) > a').find('span').should('contain', 'Menu set up');
        cy.get('#bolt--sidebar ul li:nth-child(11) ul > li:nth-child(7) > a').find('span').should('contain', 'Routing configuration');
        cy.get('#bolt--sidebar ul li:nth-child(11) ul > li:nth-child(8) > a').find('span').should('contain', 'All configuration files');
    })
});
