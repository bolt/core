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
      | Username / Email |
      | Roles |
      | Session age |
      | Last IP |
      | Actions |
    And I should see 5 rows in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" table
    And the data in the 1st row of the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" table should match:
      | col1 | col2 | col3 | col4 | col6 | col7 |
      | 1 | Admin | admin / admin@example.org | ROLE_ADMIN | 127.0.0.1 | Edit |

  @javascript
  Scenario: Disable/enable user
    When I am logged in as "jane_admin" with password "jane%1"
    Then I should be on "/bolt/"
    And I should see "Bolt Dashboard"

    Given I logout
    And I am logged in as "admin"
    When I am on "/bolt/users"
    #disable button for given user
    And I click "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(3) > td:nth-child(7) > a:nth-child(2)"
    Then I should see "Enable" in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(3) > td:nth-child(7) > a:nth-child(2)" element

    Then I logout
    When I am logged in as "jane_admin" with password "jane%1"
    Then I should be on "/bolt/login"
    And I should see "User is disabled"

    When I am logged in as "admin"
    And I am on "/bolt/users"
    And I click "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(3) > td:nth-child(7) > a:nth-child(2)"
    Then I should see "Disable" in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(3) > td:nth-child(7) > a:nth-child(2)" element

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
    And I scroll "#multiselect-locale > div > div.multiselect__select" into view
    And I click "#multiselect-locale > div > div.multiselect__select"
    And I scroll "#multiselect-locale > div > div.multiselect__content-wrapper > ul > li:nth-child(1)" into view
    And I click "#multiselect-locale > div > div.multiselect__content-wrapper > ul > li:nth-child(1)"
    And I scroll "#multiselect-roles > div > div.multiselect__select" into view
    And I click "#multiselect-roles > div > div.multiselect__select"
    And I scroll "#multiselect-roles > div > div.multiselect__content-wrapper > ul > li:nth-child(1) > span" into view
    And I click "#multiselect-roles > div > div.multiselect__content-wrapper > ul > li:nth-child(1) > span"

    When I scroll "#editcontent > button" into view
    And I press "Save changes"

    Then I should be on "/bolt/users"
    And I should see 6 rows in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" table
    And I should see "test_user" in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" element
    And I should see "test_user@example.org" in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" element
    And I should see "Test user" in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" element

    #delete button for new user
    When I click "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(5) > td:nth-child(7) > a.btn.btn-danger.mb-3.text-nowrap"
    Then I should be on "/bolt/users"
    And I should see 5 rows in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1)" table
    And I should not see "test_user"
    And I should not see "test_user@example.org"

  @javascript
  Scenario: Edit user
    Given I am logged in as "admin"
    And I am on "/bolt/users"
    Given I am logged in as "admin"
    And I am on "/bolt/users"
    #edit on tom_admin
    When I click "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(1) > tbody > tr:nth-child(5) > td:nth-child(7) > a:nth-child(1)"
    Then I should be on url matching "\/bolt\/user\-edit\/[0-9]+"

    When I fill in the following:
      | username | tom_admin_changed |
      | displayName | Tom Doe CHANGED |
      | email | tom_admin_changed@example.org |
    And I scroll "#editcontent > button" into view
    And I press "Save changes"

    Then I should be on "/bolt/users"
    And I should see "tom_admin_changed"
    And I should see "Tom Doe CHANGED"
    And I should see "tom_admin_changed@example.org"

  @javascript
  Scenario: View current sessions
    Given I am logged in as "admin"
    When I am on "/bolt/users"
    Then I should see "Current sessions"
    And the columns schema of the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(4)" table should match:
      | columns |
      | # |
      | Username |
      | Session age |
      | Session expires |
      | IP address |
      | Browser / platform |
    And I should see 1 row in the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(4)" table
    And the data in the 1st row of the "body > div.admin > div.admin__body > div.admin__body--container > main > table:nth-child(4)" table should match:
      | col1 | col2 | col4 | col5 |
      | 1 | admin | in 13 days | 127.0.0.1 |
