Feature: Visiting Dashboard
  @javascript
  Scenario: As an admin I want to see the Dashboard page
    Given I am logged in as "admin"
    When I am on "/bolt/"
    Then I should see 8 ".listing--container" elements
