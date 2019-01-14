Feature: Homepage
    Scenario: As a user I want to display Homepage
        When I visit the "homepage" page
        Then there is element "title" with value "t:Bolt Four Website"
        And the "recent_pages_list" element is visible