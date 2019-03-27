Feature: Display a listing of records
    Scenario: As a user I want to display a listing of records
        When I visit the "listing_records" page
        Then I wait for "heading" element to appear

        Then I scroll to the "pagination" element
        And I click the "next" element

        Then I wait for "heading" element to appear
        Then there is element "current" with text "2"