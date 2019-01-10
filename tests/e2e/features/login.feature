Feature: Logging in
    Scenario: Login as Admin to the Dashboard
        When I visit the "login" page
        And I fill the "login" form with:
        | username | admin   |
        | password | admin%1 |
        And I click the "login_button" element
        Then the "dashboard" page is displayed
        And there is element "profile_text" with value "t:Hey, Admin!"