Feature: Setcontent
@wip
    Scenario: As a user I want to see the results of Setcontent
        When I visit the "setcontent" page

        # Check if all Setcontent blocks have results.
        Then there is element "results_one" with text "yes"
        And there is element "results_one" with text "yes"
        And there is element "results_two" with text "yes"
        And there is element "results_four" with text "yes"
        And there is element "results_five" with text "yes"
        And there is element "results_six" with text "yes"

        # Check if nr 3 (with 'returnsingle') has a result
        Then there is element "three_s1" with text "2"
        And there is element "three_s2" with text "published"

        # None of the listings should contain [no], for incorrect sorting
        Then there is no element "body" containing "[no]" text