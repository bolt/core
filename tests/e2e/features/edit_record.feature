Feature: Edit record
    Scenario: As an Admin I want to change title of a record
        Given I am logged in as "admin"

        When I visit the "pages_overview" page
        And I click the "edit_button" element
        Then I wait for "title_field" element to appear

        When I fill the "title_field" field with "Changed title"
        And I click the "save_button" element

        When I visit the "pages_overview" page
        Then there is element "record_title" with text "Changed title"