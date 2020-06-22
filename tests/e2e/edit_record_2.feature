Feature: Preview record after editing

  @javascript
  Scenario: As an Admin I want to preview an edited record
    Given I am logged in as "admin"
    When I am on "/bolt/edit/30"
    When I fill in "field-title" with "Check preview"
    And I scroll ".btn-primary" into view
    And I press "Preview"
    And I switch to tab "1"
    Then I should see "Check preview" in the ".title" element

    When I switch to tab "0"
    And I reload the page
    And I wait 1 second
    Then the "field-title" field should not contain "Check preview"

  @javascript
  Scenario: As an Admin I want to view saved changes of a record
    Given I am logged in as "admin"
    When I am on "/bolt/edit/2"

    When I fill in "field-heading" with "This is the title in the wrong locale"
    And I scroll "Save changes" into view
    And I press "Save changes"

    When I am on "/bolt/edit/2?edit_locale=nl"
    And I fill in "field-heading" with "This is the title in the correct locale"
    And I scroll "Save changes" into view
    And I press "Save changes"

    Then I should be on "/bolt/edit/2?edit_locale=nl"

    When I scroll "View saved version" into view
    And I click "View saved version"

    Then I should not see "This is the title in the wrong locale"
    And I should see "This is the title in the correct locale"
