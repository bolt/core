Feature: Setcontent

  @javascript
  Scenario: As a user I want to see the results of Setcontent
    When I am on "/page/setcontent-test-page"
    Then I should see "yes" in the "#results-one" element
    And I should see "yes" in the "#results-two" element
    And I should see "yes" in the "#results-four" element
    And I should see "yes" in the "#results-five" element
    And I should see "yes" in the "#results-six" element

    Then I should see "2" in the "#three .s1" element
    And I should see "published" in the "#three .s2" element

    And I should not see "[no]" in the "main" element

  Scenario: As a user I want to see the results of Setcontent on a translated page
    When I am on "/nl/page/setcontent-test-page"
    Then I should see "yes" in the "#results-one" element
    And I should see "yes" in the "#results-two" element
    And I should see "yes" in the "#results-four" element
    And I should see "yes" in the "#results-five" element
    And I should see "yes" in the "#results-six" element

    Then I should see "2" in the "#three .s1" element
    And I should see "published" in the "#three .s2" element

    And I should not see "[no]" in the "main" element
