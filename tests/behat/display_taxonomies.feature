Feature:
  @javascript
  Scenario: As a user I want to see taxonomies on a single record
    When I am on "/page/this-is-a-record-in-the-entries-contenttype"
    Then I wait for ".title"
    And I should see 2 ".taxonomy-categories" elements
    And I should see 4 ".taxonomy-tags" elements

  Scenario: As an admin I want to see a listing of a taxonomy
    When I am on "/page/this-is-a-record-in-the-entries-contenttype"
    Then I wait for ".title"
    And I follow "Movies"
    Then I should be on "/categories/movies"
    And I should see at least 3 ".article" elements