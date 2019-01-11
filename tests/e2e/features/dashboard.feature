Feature: Visiting Dashboard
    Scenario: As an admin I want to see Dashboard page

#        Given I am logged in as "admin"
        When I visit the "login" page
        And I fill the "login" form with:
            | username | admin   |
            | password | admin%1 |
        And I click the "login_button" element
        Then the "dashboard" page is displayed

        When I visit the "dashboard" page
        Then there is element "header" with value "t:Dashboard"