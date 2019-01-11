Feature: Logging in
    Scenario: Login as Admin to the Dashboard
        When I visit the "homepage" page
        Then there is element "title" with value "t:Bolt Four Website"
        Then the "record_list" element is visible