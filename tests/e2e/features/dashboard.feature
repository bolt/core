Feature: Visiting Dashboard
    @wip
    Scenario: As an admin I want to see Dashboard page
        Given I am logged in as "admin"
        When I visit the "dashboard" page
        Then there is element "header" with text "Dashboard"
        And the "rows" element is visible
        And there are "equal 5" "rows" elements
        