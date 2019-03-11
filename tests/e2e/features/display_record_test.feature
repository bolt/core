Feature: Test field output
    @wip
    Scenario: As a user I want to see how fields are escaped

        When I visit the "single_test" page with parameters:
            | slug | title-of-the-test |
        Then I wait for "title" element to appear

        And there is element "text_markup_a" with text "Text with markup allowed."
        And there is element "text_markup_b" with text "Text with markup allowed."
        And there is element "text_markup_c" with text "Text with <em>markup allowed</em>."

        And there is element "text_plain_a" with text "Text with <strong>no</strong> markup allowed."
        And there is element "text_plain_b" with text "Text with no markup allowed."
        And there is element "text_plain_c" with text "Text with <strong>no</strong> markup allowed."

        And there is element "html_field" with text "HTML field with simple HTML in it."
        And there is element "markdown_field" with text "Markdown field with simple Markdown in it."
        And there is element "textarea_field" with text "Textarea field with <em>simple</em> HTML in it."
