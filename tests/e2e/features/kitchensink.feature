Feature: Visiting the Kitchensink
    Scenario: As an admin I want to see the Kitchensink page
        Given I am logged in as "admin"
        When I visit the "kitchensink" page
        Then there is element "header" with text "Kitchensink"
        And there is element "header" with text "different things"
        And there is element "title" with text "Kitchensink"
        And there is element "subtitle" with text "different things"

        And there are "equal 18" "buttons" elements

        And there is element "field" with text "FooBar"

        And there is element "title_label" with text "Title:"
        And the "title_field" element is visible
        And there is element "title_postfix" with text "shown on the homepage"

