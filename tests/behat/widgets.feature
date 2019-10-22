Feature: Visiting Dashboard

  @javascript
  Scenario: As an admin I want to see the News Widget
    Given I am logged in as "admin"
    When I am on "/bolt/"
    Then I should see "Latest Bolt News" in the "#widget-news-widget" element
