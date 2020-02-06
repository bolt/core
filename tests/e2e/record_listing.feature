Feature: Record listing

  @javascript
  Scenario: As an Admin I want to navigate over record listing
    Given I am logged in as "admin"
    And I scroll "a[rel=next]" into view
    And I follow "Next"
    Then I should see an "#listing .listing__row .is-details a" element

  @javascript
  Scenario: As a user I want to see contenttype listing
    When I am on "/pages"
    Then I should see 6 "article" elements
