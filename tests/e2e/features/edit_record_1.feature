Feature: Edit record

    Scenario: As an Admin I want to change title of a record
        Given I am logged in as "admin"

        When I visit the "edit_record" page with parameters:
            | id | 30 |
        Then I wait for "title_field" element to appear

        When I fill the "title_field" field with "Changed title"
        And I click the "status_select" element
        And I click the "status_published" element
        And I click the "save_button" element
        Then I wait for "title_field" element to appear

        When I visit the "single_record" page with parameters:
            | id | 30 |
        Then there is element "title" with text "Changed title"

    Scenario: As an Admin I want to change title of a record in another language
        Given I am logged in as "admin"

        When I visit the "edit_record" page with parameters:
            | id | 1 |
        Then I wait for "title_field" element to appear
        When I fill the "title_field" field with "Changed title EN"
        And I click the "save_button" element

        Then I wait for "lang_select" element to appear
        And I click the "lang_select" element
        And I click the "lang_nl" element

        Then I wait for "title_field" element to appear
        When I fill the "title_field" field with "Changed title NL"
        And I click the "save_button" element

        # Note, see https://github.com/TheSoftwareHouse/Kakunin/pull/157
        # @todo update, when the mentioned PR lands in stable Kakunin
        When I visit the "edit_record" page with parameters:
            | id          | 1?edit_locale=nl  |
        Then I wait for "title_field" element to appear
        Then there is element "title_field" with text "Changed title NL"

        When I visit the "edit_record" page with parameters:
            | id          | 1?edit_locale=nl&_locale=nl |
        Then I wait for "title_field" element to appear
        Then there is element "title_field" with text "Changed title NL"

        When I visit the "edit_record" page with parameters:
            | id          | 1?edit_locale=en&_locale=nl |
        Then I wait for "title_field" element to appear
        Then there is element "title_field" with text "Changed title EN"

        When I visit the "single_record" page with parameters:
            | id      | 1?_locale=nl |
        Then there is element "title" with text "Changed title NL"

        When I visit the "single_record" page with parameters:
            | id      | 1  |
        Then there is element "title" with text "Changed title NL"

        When I visit the "single_record" page with parameters:
            | id      | 1?_locale=en |
        Then there is element "title" with text "Changed title EN"

        When I visit the "single_record" page with parameters:
            | id      | 1  |
        Then there is element "title" with text "Changed title EN"

    Scenario: As an Admin I want to be able to make use of the embed field
        Given I am logged in as "admin"

        When I visit the "edit_record" page with parameters:
            | id | 44 |
        Then I wait for "title_field" element to appear
        Then I click the "tab_media" element
        When I fill the "embed_field" field with "https://www.youtube.com/watch?v=x4IDM3ltTYo"
        And I wait for "2" seconds
        Then there is element "embed_title" with text "Silversun Pickups - Nightlight (Official Video)"
        And there is element "embed_author" with text "Silversun Pickups"
        And there is element "embed_width" with text "480"
        And there is element "embed_height" with text "270"
