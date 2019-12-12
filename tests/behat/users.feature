Feature: Users & Permissions
  @javascript
  Scenario: View users and permissions
    Given I am logged in as "admin"
    When I follow "Configuration"
    #Users & Permissions
    When I click "body > div.admin > div.admin__body > div.admin__body--container.admin__body--container--has-sidebar > main > p > a:nth-child(1)"
    Then I should be on "/bolt/users"
    #users table
    And the columns schema of the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" table should match:
      | columns |
      | # |
      | Display name |
      | Username |
      | Email |
      | Roles |
      | Last registered |
      | Last IP |
      | Actions |
    And I should see 5 rows in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" table
    And the data in the 1st row of the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" table should match:
      | col1 | col2 | col3 | col4 | col5 | col6 | col7 | col8 |
      | 1 | Admin | admin | admin@example.org | ROLE_ADMIN | today | 127.0.0.1 | Edit |

  @javascript
  Scenario: Disable/enable user
    When I am logged in as "jane_admin" with password "jane%1"
    Then I should be on "/bolt/"
    And I should see "Bolt Dashboard"

    Given I logout
    And I am logged in as "admin"
    When I am on "/bolt/users"
    #disable button for given user
    And I click "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(3) > td:nth-child(8) > a:nth-child(2)"
    Then I should see "Enable" in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(3) > td:nth-child(8) > a:nth-child(2)" element

    Then I logout
    When I am logged in as "jane_admin" with password "jane%1"
    Then I should be on "/bolt/login"
    And I should see "User is disabled"

    When I am logged in as "admin"
    And I am on "/bolt/users"
    And I click "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(3) > td:nth-child(8) > a:nth-child(2)"
    Then I should see "Disable" in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(3) > td:nth-child(8) > a:nth-child(2)" element

    Then I logout
    Then I am logged in as "jane_admin" with password "jane%1"
    Then I should be on "/bolt/"
    And I should see "Bolt Dashboard"
    Then I logout

  @javascript
  Scenario: Create/delete user
    Given I am logged in as "admin"
    When I am on "/bolt/users"
    And I scroll "body > div.admin > div.admin__body > div.admin__body--container > main > p > a" into view
    And I follow "Add User"

    Then I should be on "/bolt/user-edit/0"
    And I should see "New User" in the ".admin__header--title" element

    When I fill in the following:
      | username | test_user |
      | displayName | Test user |
      | password | test%1 |
      | email | test_user@example.org |
