Feature: Homepage
  @javascript
  Scenario: I want to display Homepage
    When I am on "/"
    Then I should see "Bolt Core Git Clone"
    And I should see "Recent Pages"