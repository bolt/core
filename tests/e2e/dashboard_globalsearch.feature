Feature: Global Search on Dashboard

  @javascript
  Scenario: As an Admin I want to filter content
  Given I am logged in as "admin"
  And I am on "/bolt"

  Then I should see "Bolt Dashboard"

  When I fill "#global-search" element with "a"
  And I press "Search"

  Then I should be on "/bolt/?filter=a"
  Then I should see 8 ".listing--container" elements
  And I should see "All content, filtered by 'a'"

  Then I wait 1 seconds

  When I fill "#global-search" element with "Entries"
  And I press "Search"
  Then I should be on "/bolt/?filter=Entries"
  Then I should see 1 ".listing--container" elements
  And I should see "Entries" in the ".listing--container" element

  Then I wait 1 seconds

  When I fill "#global-search" element with ""
  And I press "Search"
  Then I should be on "/bolt/"
  And I should see 8 ".listing--container" elements
