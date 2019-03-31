Feature: Frontend menu
    @wip
    Scenario: As a user I want to see the menu in the frontend
        When I visit the "homepage" page
        Then there is element "menu_first" with text "Home"
        And there is element "menu_last" with text "The Bolt site"
        And there are "eq 4" "menu_sub" elements
