Feature: Display record
    Scenario: As a user I want to display a single record
        When I visit the "single_record" page
        Then I wait for "visibilityOf" of the "title" element
        And the "edit_button" element is visible