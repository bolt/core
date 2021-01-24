Feature: Bulk content listing actions
  @javascript
  Scenario: As an admin I want to select all items on a contentlisting page
    Given I am logged in as "admin"
    And I am on "/bolt/content/pages"
    Then I should see an "label[for='selectAll']" element
    And I click ".listing__filter .custom-checkbox"

    Then I should see "8 Pages Selected" in the ".admin__body--aside .card-header" element
    And I should see "Select option" in the ".admin__body--aside .card-body .multiselect" element
    And I should see "Apply to all"
    And the 1st "Apply to all" button should be disabled

  @javascript

  Scenario: As an Admin I want to change the status of several tests at once
    Given I am logged in as "admin"
    And I am on "/bolt/content/tests"

    When I click ".listing__filter .custom-checkbox"

    And I click "aside .card-body .multiselect__select"
    And I wait 0.1 second
    Then I should see "Change status to 'publish'"
    And I should see "Change status to 'draft'"
    And I should see "Change status to 'held'"
    And I should see "Delete"

#    When I click "Change status to 'draft'"
    When I click "aside .card-body .multiselect__content-wrapper > ul > li:nth-child(2)"
    And I wait 0.1 second
    Then the 1st "Apply to all" button should be enabled

    When I press "Apply to all"

    Then I should be on "/bolt/content/tests"
    And I should see 8 ".listing__records .is-meta .status.is-draft" elements
    And I should not see an ".listing__records .is-meta .status.is-published" element
    And I should not see an ".listing__records .is-meta .status.is-held" element

    When I click ".listing__filter .custom-checkbox"
    And I click "aside .card-body .multiselect__select"

#    And I click "Change status to 'publish'"
    And I click "aside .card-body .multiselect__content-wrapper > ul > li:nth-child(1)"
    And I wait 0.1 second
    Then the 1st "Apply to all" button should be enabled

    When I press "Apply to all"

    Then I should be on "/bolt/content/tests"
    And I should see 8 ".listing__records .is-meta .status.is-published" elements
    And I should not see an ".listing__records .is-meta .status-is-draft" element
    And I should not see an ".listing__records .is-meta .status.is-held" element
    And I should see "Status changed successfully"
