Feature: Display single record
    Scenario: As a user I want to display a single record
        When I visit the "single_record" page with parameters:
            | id | 1 |
        Then I wait for "title" element to appear
        And the "edit_button" element is not visible

    Scenario: As an admin I want to see edit link on single record page
        Given I am logged in as "admin"
        When I visit the "single_record" page with parameters:
            | id | 1 |
        Then I wait for "title" element to appear
        And the "edit_button" element is visible

    Scenario: As a user I want to see the difference between records with a "Title" and a "Heading"
        When I visit the "single_record" page with parameters:
            | id | 2 |
        Then I wait for "heading" element to appear
        And the "title" element is not visible