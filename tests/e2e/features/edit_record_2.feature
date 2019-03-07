Feature: Preview record after editing

    @wip
    Scenario: As an Admin I want to preview an edited record
        Given I am logged in as "admin"

        When I visit the "edit_record" page with parameters:
            | id | 30 |
        Then I wait for "title_field" element to appear

        When I fill the "title_field" field with "Check preview"
        And I click the "preview_button" element

        When I switch to window number "2" of a browser
        Then there is element "frontend_title" with text "Check preview"

    @wip
    Scenario: As an Admin I want to check the "saved version"
        Given I am logged in as "admin"

        When I visit the "edit_record" page with parameters:
            | id | 30 |
        Then I wait for "title_field" element to appear

        When I fill the "title_field" field with "Check saved version"
        And I click the "dropdown_button" element
        And I click the "viewsaved_button" element

        When I switch to window number "3" of a browser
        Then there is element "frontend_title" with text "Changed title"
