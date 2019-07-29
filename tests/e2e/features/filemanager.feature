Feature: Filemanager

    Scenario: As an Admin I want to see the files in the "Files" section
        Given I am logged in as "admin"

        When I visit the "filemanager" page with parameters:
            | area | themes |

        Then there is element "header" with text "Theme files"
        And there is element "path" with text "Path: themes/"

        When I visit the "filemanager" page with parameters:
            | area | files |

        Then there is element "header" with text "Content files"
        And there is element "path" with text "Path: files/"

        # @todo Add tests for uploading files, and verifying that they're there
        