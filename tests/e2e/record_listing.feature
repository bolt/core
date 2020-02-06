Feature: Record listing

  @javascript
  Scenario: As an Admin I want to navigate over record listing
    Given I am logged in as "admin"
    And I scroll "a[rel=next]" into view
    And I follow "Next"
    Then I should see an "#listing .listing__row .is-details a" element

  @javascript
  Scenario: As an Admin I want to sort content
    Given I am logged in as "admin"
    And I am on "/bolt"

    When I follow "Entries"
    Then I should be on "/bolt/content/entries"
    And I should see "Entries" in the ".admin__header--title" element
    And I should see "Contentlisting"

    When I select "Author" from "sortBy"
    And I press "Filter"

    Then I should be on "/bolt/content/entries?sortBy=author&filter="
    And I should see "Admin" in the ".listing--container:nth-of-type(1)" element
    And I should not see "Admin" in the ".listing--container:nth-of-type(10)" element

  @javascript
  Scenario: As an Admin I want to filter content
    Given I am logged in as "admin"
    And I am on "/bolt/content/entries"

    Then I should see "Contentlisting"

    When I fill "filter" element with "a"
    And I press "Filter"

    Then I should be on "/bolt/content/entries?sortBy=&filter=a"
    Then I should see 10 ".listing--container" elements

    When I fill "filter" element with "Entries"
    And I press "Filter"

    Then I should be on "/bolt/content/entries?sortBy=&filter=Entries"
    Then I should see 1 ".listing--container" elements
    And I should see "Entries" in the ".listing--container" element

    When I fill "filter" element with ""
    And I press "Filter"
    Then I should be on "/bolt/content/entries?sortBy=&filter="
    And I should see 10 ".listing--container" elements