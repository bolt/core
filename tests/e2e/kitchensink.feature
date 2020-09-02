Feature: Visiting the Kitchensink

  @javascript
  Scenario: As an admin I want to see the Kitchensink page
    Given I am logged in as "admin"
    When I am on "/bolt/kitchensink"
    Then I should see "Kitchensink" in the ".admin__header--title" element
    And I should see "different things" in the ".admin__header--title" element
    And I should see "Lorem ipsum dolor sit amet, consectetur adipiscing elit" in the "h2" element
    And I should see "different things" in the "h3" element

    And I should see 20 "section.buttons button.btn" elements

    And the "foo" field should contain "FooBar"

    And I should see "Title:" in the "label[for='field-title']" element
    And I should see an "input#field-title" element
    And I should see "shown on the homepage" in the "div#field-title_postfix" element