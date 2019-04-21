Feature: Visiting Dashboard

    Scenario: As an admin I want to see API page
        Given I am logged in as "admin"
        When I visit the "backend_api" page
        Then there is element "header" with text "Bolt API"

