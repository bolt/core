Feature: Visiting Dashboard

  @javascript
  Scenario: As an admin I want to see Dashboard page
    Given I am logged in as "admin"
    Then I should see "Dashboard" in the ".admin__header--title" element
    And I should see 8 ".listing__row" elements
    And I should see an ".listing__row" element