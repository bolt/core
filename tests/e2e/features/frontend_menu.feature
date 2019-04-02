Feature: Frontend menu

    Scenario: As a user I want to see the menu in the frontend
        When I visit the "homepage" page
        Then there is element "menu_first" with text "Home"
        And there is element "menu_last" with text "The Bolt site"
        And there are "eq 4" "menu_sub" elements

    @wip
    Scenario: As a user I want to see the multi-level in the frontend
        When I visit the "single_test" page with parameters:
            | slug | title-of-the-test |
        Then there is element "menu_item1" with text "Item 1"
        And there is element "menu_item11" with text "Item 1.1"
        And there is element "menu_item112" with text "Item 1.1.2"
        And there is element "menu_item1122" with text "Item 1.1.2.2"
