Feature: Edit record

    Scenario: As an Admin I want to change title of a record
        Given I am logged in as "admin"

        When I visit the "edit_record" page with parameters:
            | id | 5 |
        Then I wait for "title_field" element to appear

        When I fill the "title_field" field with "Changed title"
        And I click the "status_select" element
        And I click the "status_published" element
        And I click the "save_button" element

        When I visit the "single_record" page with parameters:
            | id | 5 |
        Then there is element "title" with text "Changed title"

    @wip
    Scenario: As an Admin I want to change title of a record in another language
        Given I am logged in as "admin"

        When I visit the "edit_record" page with parameters:
            | id | 1 |
        Then I wait for "title_field" element to appear
        When I fill the "title_field" field with "Changed title EN"
        And I click the "save_button" element

        And I click the "lang_select" element
        And I click the "lang_nl" element
        And I click the "change_lang" element

        Then I wait for "title_field" element to appear
        When I fill the "title_field" field with "Changed title NL"
        And I click the "save_button" element

        When I visit the "single_record" page with parameters:
            | id          | 1  |
            | edit_locale | nl |
        Then there is element "title" with text "Changed title NL"

        When I visit the "single_record" page with parameters:
            | id          | 1  |
            | edit_locale | nl |
            | _locale     | nl |
        Then there is element "title" with text "Changed title NL"

        When I visit the "single_record" page with parameters:
            | id          | 1  |
            | edit_locale | en |
            | _locale     | nl |
        Then there is element "title" with text "Changed title NL"

        When I visit the "display_record" page with parameters:
            | id      | 1  |
            | _locale | nl |
        Then there is element "title" with text "Changed title NL"
