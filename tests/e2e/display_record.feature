Feature: Display single record
  @javascript
  Scenario: As a user I want to display a single record
    When I am on "/entry/this-is-a-record-in-the-entries-contenttype"
    Then I wait for ".title"
    And I should not see an ".edit-link" element

  @javascript
  Scenario: As an admin I want to see edit link on single record page
    Given I am logged in as "admin"
    And I am on "/entry/this-is-a-record-in-the-entries-contenttype"
    Then I wait for ".title"
    And I should see "Edit" in the ".edit-link" element

  @javascript
  Scenario: As a user I want to see the difference between records with a "Title" and a "Heading"
    Given I am on "/page/2"
    Then I wait for ".heading"
    And I should not see a ".title" element

  @javascript
  Scenario: As a user I want to see the correct canonical URL for a page
    When I am on "/page/this-is-a-page"
    Then the "link[rel='canonical']" field should have "href='http://127.0.0.1:8088/page/this-is-a-page'" attribute

    When I am on "/page/2"
    Then the "link[rel='canonical']" field should have "href='http://127.0.0.1:8088/page/this-is-a-page'" attribute

    When I am on "/en/page/this-is-a-page"
    Then the "link[rel='canonical']" field should have "href='http://127.0.0.1:8088/page/this-is-a-page'" attribute

    When I am on "/nl/page/2"
    Then the "link[rel='canonical']" field should have "href='http://127.0.0.1:8088/nl/page/this-is-a-page'" attribute
