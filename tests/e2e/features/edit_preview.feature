Feature: Preview record after editing

    @wip
    Scenario: As an Admin I want to preview an edited record
        Given I am logged in as "admin"

        When I visit the "edit_record" page with parameters:
            | id | 30 |
        Then I wait for "title_field" element to appear

        When I fill the "title_field" field with "Check preview"
        And I click the "preview_button" element
        Then I wait for "frontend_title" element to appear
        Then there is element "frontend_title" with text "Check preview"
