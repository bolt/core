/// <reference types="cypress" />

describe('As an Admin I want to be able to make use of the embed, infobox and image fields', () => {
    it('checks if an admin can use the embed field', () => {
        cy.login();
        cy.visit('/bolt/edit/44');
        cy.get('a[id="media-tab"]').click();

        cy.get('input[name="fields[embed][url]"]').clear();
        cy.get('input[name="fields[embed][url]"').type('https://www.youtube.com/watch?v=x4IDM3ltTYo');
        cy.wait(2000);
        cy.get('button[class="btn btn-tertiary refresh"]').should('be.enabled');

        cy.get('input[name="fields[embed][title]"]').should('have.value', 'Silversun Pickups - Nightlight (Official Video)');
        cy.get('input[name="fields[embed][authorname]"]').should('have.value', 'Silversun Pickups');
        cy.get('input[name="fields[embed][width]"]').should('have.value', '200');
        cy.get('input[name="fields[embed][height]"]').should('have.value', '113');

        cy.get('.editor__embed .remove').click();
        cy.get('input[name="fields[embed][title]"]').should('have.value', '');
        cy.get('input[name="fields[embed][authorname]"]').should('have.value', '');
        cy.get('input[name="fields[embed][width]"]').should('have.value', '');
        cy.get('input[name="fields[embed][height]"]').should('have.value', '');
    })

    it('checks if an admin can see the infobox field', () => {
        cy.login();
        cy.visit('/bolt/edit/38');

        cy.get("label[for='field-email']").should('exist');
        cy.get("label[for='field-email']").find('i').its('length').should('eq', 1);

        cy.get('label[for="field-email"]').scrollIntoView();
        cy.get("label[for='field-email'] > i").trigger('mouseover');
        cy.get('.popover-header').should('contain', 'Email').should('be.visible');
        cy.get('.popover-body').should('contain', 'This is an info box shown as a popover next to the field label.').should('be.visible');
    })

    it('checks if an admin can reset an image field', () => {
        cy.login();
        cy.visit('/bolt/edit/40');
        cy.get('a[id="media-tab"]').click();

        cy.get('label[for=field-image]').should('contain', 'Image');
        cy.get('.form-control').eq(10).should('not.equal', '');
        cy.get('.form-control').eq(11).should('not.equal', '');

        cy.get('button[class="btn btn-sm btn-hidden-danger"]').should('contain', 'Remove').eq(0).click();
        cy.get('input[name="fields[image][filename]"]').should('be.empty');
        cy.get('input[name="fields[image][alt]"]').should('be.empty');
    })
});

describe('As an Admin I want to be able to make use of the date & datetime fields', () => {
    before(() => {
        // First, switch an account to a locale which isn't using an AM/PM system (french for example)
        cy.login('john_editor', 'john%1');

        cy.visit('/bolt/profile-edit');
        cy.get('#multiselect-user_locale > div > div.multiselect__select').scrollIntoView();
        cy.get('#multiselect-user_locale > div > div.multiselect__select').click();
        cy.contains('French (français, fr)').click();

        cy.get('#edituser > button').scrollIntoView();
        cy.get('form[id="edituser"]').submit();

        cy.visit('/bolt/logout');
    })

    after(() => {
        cy.visit('/bolt/logout');

        // Revert the locale to avoid surprises in other tests
        cy.login('john_editor', 'john%1');

        cy.visit('/bolt/profile-edit');
        cy.get('#multiselect-user_locale > div > div.multiselect__select').scrollIntoView();
        cy.get('#multiselect-user_locale > div > div.multiselect__select').click();
        cy.contains('English (English, en)').click();

        cy.get('#edituser > button').scrollIntoView();
        cy.get('form[id="edituser"]').submit();

        cy.visit('/bolt/logout');
    })

    it('checks if an admin can use the date field', () => {
        cy.login();
        cy.visit('/bolt/edit/44');
        cy.get('a[id="other-tab"]').click();

        cy.get('#field--field-date button[aria-label="Date picker"]').click();
        cy.get('#field--field-date input.editor--date').should('have.class', 'active');

        cy.get('.flatpickr-calendar.open select.flatpickr-monthDropdown-months').select('March');
        cy.get('.flatpickr-calendar.open input.cur-year').type('2019');
        cy.get('.flatpickr-calendar.open span[aria-label="March 15, 2019"]').click();

        cy.get('input[name="fields[date]"]').should('have.value', '2019-03-15 00:00:00');
    })

    it('checks if an admin can use the datetime field with an AM time (with AM/PM selector)', () => {
        cy.login();
        cy.visit('/bolt/edit/44');
        cy.get('a[id="other-tab"]').click();

        cy.get('#field--field-datetime button[aria-label="Date picker"]').click();
        cy.get('#field--field-datetime input.editor--date').should('have.class', 'active');

        cy.get('.flatpickr-calendar.open select.flatpickr-monthDropdown-months').select('May');
        cy.get('.flatpickr-calendar.open input.cur-year').type('2020');
        cy.get('.flatpickr-calendar.open input.flatpickr-hour').type('11');
        cy.get('.flatpickr-calendar.open input.flatpickr-minute').type('30');
        cy.get('.flatpickr-calendar.open span.flatpickr-am-pm').then(($element) => {
            if($element.html() === 'PM') {
                // Switch to AM
                $element.click();
            }
        });
        cy.get('.flatpickr-calendar.open span[aria-label="May 10, 2020"]').click();

        cy.get('input[name="fields[datetime]"]').should('have.value', '2020-05-10 11:30:00');
    })

    it('checks if an admin can use the datetime field with a PM time (with AM/PM selector)', () => {
        cy.login();
        cy.visit('/bolt/edit/44');
        cy.get('a[id="other-tab"]').click();

        cy.get('#field--field-datetime button[aria-label="Date picker"]').click();
        cy.get('#field--field-datetime input.editor--date').should('have.class', 'active');

        cy.get('.flatpickr-calendar.open select.flatpickr-monthDropdown-months').select('July');
        cy.get('.flatpickr-calendar.open input.cur-year').type('2020');
        cy.get('.flatpickr-calendar.open input.flatpickr-hour').type('22');
        cy.get('.flatpickr-calendar.open input.flatpickr-minute').type('30');
        cy.get('.flatpickr-calendar.open span.flatpickr-am-pm').then(($element) => {
            if($element.html() === 'AM') {
                // Switch to PM
                $element.click();
            }
        });
        cy.get('.flatpickr-calendar.open span[aria-label="July 20, 2020"]').click();

        cy.get('input[name="fields[datetime]"]').should('have.value', '2020-07-20 22:30:00');
    })

    it('checks if an admin can use the datetime field with an AM time (without AM/PM selector)', () => {
        cy.login('john_editor', 'john%1');
        cy.visit('/bolt/edit/44');
        cy.get('a[id="other-tab"]').click();

        cy.get('#field--field-datetime button[aria-label="Date picker"]').click();
        cy.get('#field--field-datetime input.editor--date').should('have.class', 'active');

        cy.get('.flatpickr-calendar.open select.flatpickr-monthDropdown-months').select('janvier');
        cy.get('.flatpickr-calendar.open input.cur-year').type('2020');
        cy.get('.flatpickr-calendar.open input.flatpickr-hour').type('10');
        cy.get('.flatpickr-calendar.open input.flatpickr-minute').type('15');
        cy.get('.flatpickr-calendar.open span[aria-label="janvier 28, 2020"]').click();

        cy.get('input[name="fields[datetime]"]').should('have.value', '2020-01-28 10:15:00');
    })

    it('checks if an admin can use the datetime field with a PM time (without AM/PM selector)', () => {
        cy.login('john_editor', 'john%1');
        cy.visit('/bolt/edit/44');
        cy.get('a[id="other-tab"]').click();

        cy.get('#field--field-datetime button[aria-label="Date picker"]').click();
        cy.get('#field--field-datetime input.editor--date').should('have.class', 'active');

        cy.get('.flatpickr-calendar.open select.flatpickr-monthDropdown-months').select('juillet');
        cy.get('.flatpickr-calendar.open input.cur-year').type('2020');
        cy.get('.flatpickr-calendar.open input.flatpickr-hour').type('22');
        cy.get('.flatpickr-calendar.open input.flatpickr-minute').type('30');
        cy.get('.flatpickr-calendar.open span[aria-label="juillet 20, 2020"]').click();

        cy.get('input[name="fields[datetime]"]').should('have.value', '2020-07-20 22:30:00');
    })
});
