Feature: Logging in
  @javascript
  Scenario: As an admin I want to log in to Dashboard
    Given I am on "/bolt/login"
    When I fill in the following:
      | username | admin |
      | password | admin%1 |
    And I press "Log in"
    Then I should see "Hey, Admin"
    And I should be on "/bolt/"

  @javascript
  Scenario: As an admin I attempt to log in to Dashboard with incorrect credentials
    Given I am on "/bolt/login"
    When I fill in the following:
      | username | admin |
      | password | noadmin |
    And I press "Log in"
    Then I should be on "/bolt/login"
    And I should see "Invalid credentials"