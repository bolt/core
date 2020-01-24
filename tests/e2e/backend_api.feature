Feature: Visiting API backend page

  @javascript
  Scenario: As an admin I want to see API page
    Given I am logged in as "admin"
    When I am on "/bolt/api"
    Then I should see "Bolt API" in the ".admin__header--title" element