Feature: Visiting Dashboard
    Scenario: Login as Admin to the Dashboard
        Given I am logged in as "admin"
        When I visit the "dashboard" page
        Then the "dashboard" page is displayed
        And there is element "header" with value "t:Dashboard"