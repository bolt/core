Feature: Extension Routing
  @javascript
  Scenario: I want to see a page, added by an Extension
    When I am on "/extensions/reference/Zebedeus"
    Then I should see "Hello, Zebedeus"

    When I am on "/extensions/reference"
    Then I should see "404 Page not found"
