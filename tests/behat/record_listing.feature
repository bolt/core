Feature: Record listing

  @javascript
  Scenario: As an Admin I want to navigate over record listing
    Given I am logged in as "admin"
    And I scroll "a[rel=next]" into view
    And I follow "Next"
    Then I should see an "#listing .listing__row .is-details a" element
