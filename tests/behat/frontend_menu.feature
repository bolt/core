Feature: Frontend menu

  @javascript
  Scenario: As a user I want to see the menu in the frontend
    When I am on the homepage
    Then I should see "Home" in the ".menu .first" element
    And I should see "The Bolt site" in the ".menu .bolt-site" element
    And I should see 4 ".has-submenu li" elements

  @javascript
  Scenario: As a user I want to see the multi-level in the frontend
    When I am on "/page/title-of-the-test"
    Then I should see "Item 1" in the ".menu .item-1" element
    And I should see "Item 1.1" in the ".menu .item-1-1" element
    And I should see "Item 1.1.2" in the ".menu .item-1-1-2" element
    And I should see "Item 1.1.2.2" in the ".menu .item-1-1-2-2" element
