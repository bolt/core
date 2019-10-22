Feature: Visiting Translations page

  @testme
  @javascript
  Scenario: As an admin I want to see Translations page
    Given I am logged in as "admin"
    When I am on "/bolt/_trans"
    Then I should see "Edit Translations" in the ".admin__header--title" element

