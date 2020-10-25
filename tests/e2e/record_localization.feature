Feature: See localization info for a record
  @javascript
  Scenario: No localization link for contenttype without locales
    Given I am logged in as "admin"

    When I click "Entries"
    And I click the 1st "Edit"

    Then I should see "Edit Entry"
    And I should not see "See Localization info"

  @javascript
  Scenario: See localization link for contenttype with locales
    Given I am logged in as "admin"

    When I click "Pages"
    And I click the 1st "Edit"

    Then I should see "Edit Page"
    And I should see "See Localization info"

    When I click "See Localization info"
    Then I should be on "/bolt/edit_locales/2"

    And the columns schema of the ".table" table should match:
      | columns |
      | Field   |
      | en      |
      | nl      |
      | ja      |
      | nb      |

    And the data in the 1st row of the ".table" table should match:
      | col1 | col2 | col3 | col4 | col5 |
      | Heading Type: text | OK | OK | OK | Missing |

    And the data in the 4th row of the ".table" table should match:
      | col1 | col2 | col3 | col4 | col5 |
      | Eén plaatje Type: image | Default | Default | Default | Default |

    And I should see 4 "td a" elements

    When I click the 2nd "Edit"
    Then I should be on "/bolt/edit/2?edit_locale=nl"