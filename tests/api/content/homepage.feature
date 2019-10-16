Feature: Homepage
  @javascript
  Scenario: I want to display Homepage
    When I am on "/"
    And wait 5 seconds
    Then I should see "Bolt Core Git Clone"
    And I should see "Recent Pages"