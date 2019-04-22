Feature: Visiting Dashboard

    Scenario: As an admin I want to see the News Widget
        Given I am logged in as "admin"
        When I visit the "dashboard" page
        Then there is element "widget_title" with text "Latest Bolt News"
