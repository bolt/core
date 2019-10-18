Feature:
  @javascript
  Scenario: As a user I want to display search results
    When I am on the homepage
    Then I fill in "searchTerm" with "consequatur"
    And I press "Search"

    Then I should be on "/search"
    And I should see "Search results for 'consequatur'." in the ".search-results" element
    And I should see at least 3 "article" elements