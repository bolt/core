Feature: Filemanager

  @javascript
  Scenario: As an Admin I want to see the files in the "Files" section
    Given I am logged in as "admin"
    When I am on "/bolt/filemanager/themes"
    Then I should see "Theme files" in the ".admin__header--title" element
    And I should see "Path: themes/" in the ".path" element

    When I am on "/bolt/filemanager/files"
    Then I should see "Path: files/" in the ".path" element