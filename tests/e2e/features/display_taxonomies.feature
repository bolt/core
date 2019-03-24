Feature: Display taxonomies

    @wip
    Scenario: As a user I want to see taxonomies on a single record
        When I visit the "single_record" page with parameters:
            | id | this-is-a-record-in-the-entries-contenttype |
        Then I wait for "title" element to appear
        And there are "equal 2" "taxonomy_categories" elements
        And there are "equal 4" "taxonomy_tags" elements

    @wip

    Scenario: As an admin I want to a listing of a taxonomy
        When I visit the "single_record" page with parameters:
            | id | this-is-a-record-in-the-entries-contenttype |
        Then I wait for "title" element to appear
        And I scroll to the "first_category" element
        And I click the "first_category" element
        And there are "at least 3" "article" elements