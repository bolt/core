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

    And I scroll "#multiselect-localeswitcher div.multiselect__select" into view
    Then I click "#multiselect-localeswitcher div.multiselect__content-wrapper > ul > li:nth-child(2) > span"

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

  @javascript
  Scenario: As an Admin I want to fill in an imagelist
    Given I am logged in as "admin"
    When I am on "/bolt/edit/42"
    Then I follow "Media"
    Then I should see "Imagelist" in the "label[for='field-imagelist']" element

    #From library button of imagelist
    And I scroll "#media > div.form-group.form-fieldset.is-normal > div > div > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(2) > button" into view
    When I click "#media > div.form-group.form-fieldset.is-normal > div > div > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(2) > button"
    And I wait 1 second
    And I select "kitten.jpg" from "bootbox-input"
    And I press "OK"
    Then the "fields[imagelist][0][filename]" field should contain "kitten.jpg"
    And I wait 1 second

    When I press "Add new image"
    Then I should see 2 ".row" elements in the ".editor-imagelist" element

    #Second From library button of imagelist
    When I scroll "#media > div.form-group.form-fieldset.is-normal > div > div:nth-child(2) > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(2) > button" into view
    When I click "#media > div.form-group.form-fieldset.is-normal > div > div:nth-child(2) > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(2) > button"
    And I wait 1 second
    And I select "joey.jpg" from "bootbox-input"
    And I press "OK"
    Then the "fields[imagelist][1][filename]" field should contain "joey.jpg"
    And I wait 1 second

    #click first Move down
    When I click "#media > div.form-group.form-fieldset.is-normal > div > div:nth-child(1) > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(4) > button"
    Then the "fields[imagelist][0][filename]" field should contain "joey.jpg"
    And the "fields[imagelist][1][filename]" field should contain "kitten.jpg"

    #click second Move up
    When I click "#media > div.form-group.form-fieldset.is-normal > div > div:nth-child(2) > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(3) > button"
    Then the "fields[imagelist][0][filename]" field should contain "kitten.jpg"
    And the "fields[imagelist][1][filename]" field should contain "joey.jpg"

    #first Move up
    And the "#media > div.form-group.form-fieldset.is-normal > div > div:nth-child(1) > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(3) > button" button should be disabled
    #last Move down
    And the "#media > div.form-group.form-fieldset.is-normal > div > div:nth-child(2) > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(4) > button" button should be disabled

    #first Remove
    When I click "#media > div.form-group.form-fieldset.is-normal > div > div:nth-child(1) > div > div.row > div.col-9 > div.btn-toolbar > div:nth-child(5) > button"
    Then I should see 1 ".row" elements in the ".editor-imagelist" element
    And the "fields[imagelist][1][filename]" field should contain "joey.jpg"
