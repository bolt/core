Feature: Logging in
    Scenario: As an admin I want to log in to Dashboard
        When I visit the "login" page
        And I fill the "login_form" form with:
        | username | admin   |
        | password | admin%1 |
        And I click the "login_button" element
        Then the "dashboard" page is displayed
        And there is element "profile_text" with text "Hey, Admin!"