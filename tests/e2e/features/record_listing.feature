Feature: Record listing

  Scenario: As an Admin I want to navigate over record listing
    Given I am logged in as "admin"

    When I visit the "pages_overview" page
    And I click the "pager_next" element
    Then I wait for "record_title" element to appear