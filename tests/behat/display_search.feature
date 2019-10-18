Feature:
  @javascript
  Scenario: As a user I want to display search results
    When I am on the homepage
    Then I fill in "searchTerm" with "consequatur"
    And I press "Search"

    Then I should be on "/search"
    And I should see "Search results for 'consequatur'." in the ".search-results" element
    And I should see at least 3 "article" elements

    Then I fill in "searchTerm" with "ymnrubeyrvwearsytevsf"
    And I press "Search"

    Then I should be on "/search"
    And I should see "Search results for 'ymnrubeyrvwearsytevsf'."
    And I should see "No search results found for 'ymnrubeyrvwearsytevsf'." in the ".search-results-description" element
    And I should see 0 "article" elements

    Then I fill in "searchTerm" with ""
    And I press "Search"

    Then I should be on "/search"
    And I should see "Please provide a search term, in order to display relevant results." in the ".search-results-description" element
    And I should see 0 "article" elements