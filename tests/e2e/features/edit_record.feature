Feature: Edit record
    Scenario: As an Admin I want to change title of a record
        Given I am logged in as "admin"

        When I visit the "pages_overview" page
        And I click the "edit_button" element
        Then the "edit_record" page is displayed

        When I fill the "title_field" field with "Changed title"
        And I click the "save_button" element
        Then the "edit_record" page is displayed

        When I visit the "pages_overview" page
        Then there is element "record_title" with text "Changed title"