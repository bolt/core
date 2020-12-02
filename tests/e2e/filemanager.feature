Feature: Filemanager

  @javascript
  Scenario: As an Admin I want to see the files in the "Files" section
    Given I am logged in as "admin"
    When I am on "/bolt/filemanager/themes"
    Then I should see "Theme files" in the ".admin__header--title" element
    And I should see "Path: themes/" in the ".path" element

    When I am on "/bolt/filemanager/files"
    Then I should see "Path: files/" in the ".path" element

  @javascript
  Scenario: As an Admin I want to delete a file from the "Files" section
    Given I am logged in as "admin"
    And I am on "/bolt/filemanager/files"

    Then I should see "_b-penguin.jpeg" in the 3rd "#files-list tr" element

    When I click the 2nd ".edit-actions__dropdown-toggler"
    And I follow "Delete _b-penguin.jpeg"

    And I wait for ".modal-dialog"
    Then I should see "Are you sure you wish to delete this file?"
    When I press "OK"

    Then I should see "File deleted successfully"
    And I should not see "_b-penguin.jpeg"

  @javascript
  Scenario: As an Admin I want accidentally click delete file and want to cancel
    Given I am logged in as "admin"
    And I am on "/bolt/filemanager/files"

    Then I should see "_a-sunrise.jpeg" in the 2nd "#files-list tr" element

    When I click the 1st ".edit-actions__dropdown-toggler"
    And I follow "Delete _a-sunrise.jpeg"

    And I wait for ".modal-dialog"
    Then I should see "Are you sure you wish to delete this file?"
    When I press "Cancel"

    Then I should not see "File deleted successfully"
    And I should see "_a-sunrise.jpeg" in the 2nd "#files-list tr" element

  @javascript
  Scenario: As an Admin I want to duplicate a file
    Given I am logged in as "admin"
    And I am on "/bolt/filemanager/files"

    Then I should see "_a-sunrise.jpeg" in the 2nd "#files-list tr" element
    And I should not see "I should see _a-sunrise (1).jpeg"

    When I click the 1st ".edit-actions__dropdown-toggler"
    And I follow "Duplicate _a-sunrise.jpeg"

    Then I should be on "/bolt/filemanager/files"
    And I should see "_a-sunrise (1).jpeg"
    And I should see "_a-sunrise.jpeg"

    When I click the 2nd ".edit-actions__dropdown-toggler"
    And I follow "Duplicate _a-sunrise.jpeg"

    Then I should be on "/bolt/filemanager/files"
    And I should see "_a-sunrise (2).jpeg"
    And I should see "_a-sunrise (1).jpeg"
    And I should see "_a-sunrise.jpeg"

    # This is the end of the test. Below is cleanup.
    Then I click the 2nd ".edit-actions__dropdown-toggler"
    And I follow "Delete _a-sunrise (2).jpeg"
    And I wait for ".modal-dialog"
    And I press "OK"

    Then I click the 1st ".edit-actions__dropdown-toggler"
    And I follow "Delete _a-sunrise (1).jpeg"
    And I wait for ".modal-dialog"
    And I press "OK"

  @javascript
  Scenario: As an admin I want to create and delete a folder
    Given I am logged in as "admin"
    And I am on "/bolt/filemanager/files"

    Then I should not see "a-new-folder"

    When I fill "folderName" element with "a-new-folder"
    And I click "Create"

    Then I should see "Folder created successfully"
    And I should see "a-new-folder"

    When I fill "folderName" element with "a-new-folder"
    And I click "Create"
    Then I should see "Folder already exists"

    When I click the 1st "Delete"
    And I wait 1 second
    And I click "OK"
    Then I should see "Folder deleted successfully"
    And I should not see "a-new-folder"
