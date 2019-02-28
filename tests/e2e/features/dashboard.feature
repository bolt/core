Feature: Visiting Dashboard

    Scenario: As an admin I want to see Dashboard page
        Given I am logged in as "admin"
        When I visit the "dashboard" page
        Then there is element "header" with text "Dashboard"
        And there are "equal 6" "records" elements
        And the "first_record" element is visible
