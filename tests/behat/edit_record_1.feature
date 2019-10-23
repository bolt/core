Feature: Edit record
  @javascript
  Scenario: As an Admin I want to change title of a record
    Given I am logged in as "admin"
    When I am on "/bolt/edit/30"

    When I fill in "field-title" with "Changed title"
    And I scroll ".btn-success" into view
    And I press "Save changes"

    When I am on "/bolt/edit/30"
    Then I should see "Changed title" in the ".admin__header--title" element

  @javascript
  Scenario: As an Admin I want to change title of a record in another language
    Given I am logged in as "admin"
    When I am on "/bolt/edit/1"

    When I fill in "field-title" with "Changed title EN"
    And I scroll ".btn-success" into view
    And I press "Save changes"


    And I scroll "#metadata > form > div:nth-child(2) > div > div > div > div.multiselect__select" into view
    Then I click "#metadata > form > div:nth-child(2) > div > div > div > div.multiselect__select"
    And I click "#metadata > form > div:nth-child(2) > div > div > div > div.multiselect__content-wrapper > ul > li:nth-child(2) > span"

    When I fill in "field-title" with "Changed title NL"
    And I scroll ".btn-success" into view
    And I press "Save changes"

    When I am on "/bolt/edit/1?edit_locale=nl"
    And I wait for ".admin__header--title"
    Then the "field-title" field should contain "Changed title NL"

    When I am on "/bolt/edit/1?edit_locale=nl&_locale=nl"
    And I wait for ".admin__header--title"
    Then the "field-title" field should contain "Changed title NL"

    When I am on "/bolt/edit/1?edit_locale=en&_locale=nl"
    And I wait for ".admin__header--title"
    Then the "field-title" field should contain "Changed title EN"

    When I am on "/page/1?locale=nl"
    Then I should see "Changed title NL"

  @javascript
  Scenario: As an Admin I want to be able to make use of the embed field
    Given I am logged in as "admin"
    When I am on "/bolt/edit/44"
    Then I follow "Media"

    Then I fill in "fields[embed][url]" with "https://www.youtube.com/watch?v=x4IDM3ltTYo"
    And I wait 3 seconds
    Then the "fields[embed][title]" field should contain "Silversun Pickups - Nightlight (Official Video)"
    And the "fields[embed][authorname]" field should contain "Silversun Pickups"
    And the "fields[embed][width]" field should contain "480"
    And the "fields[embed][height]" field should contain "270"