Feature: Display search functionality

    Scenario: As a user I want to display search results
        When I visit the "homepage" page

        Then I scroll to the "search_input" element
        When I fill the "search_input" field with "consequatur"
        And I click the "search_button" element

        Then the "search" page is displayed
        And there is element "search_results_title" with text "Search results for 'consequatur'."
        And there are "at least 3" "article" elements
        And there is element "search_input" with text "consequatur"

        Then I fill the "search_input" field with "ymnrubeyrvwearsytevsf"
        And I click the "search_button" element

        Then there is element "search_results_title" with text "Search results for 'ymnrubeyrvwearsytevsf'."
        And there is element "search_results_description" with text "No search results found for 'ymnrubeyrvwearsytevsf'."

        And there are "eq 0" "article" elements

        Then I fill the "search_input" field with ""
        And I click the "search_button" element

        Then there is element "search_results_description" with text "Please provide a search term, in order to display relevant results."
        And there are "eq 0" "article" elements
