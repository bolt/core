Feature: Test field output

  @javascript
  Scenario: As a user I want to see how fields are escaped

    Given I am on "/test/title-of-the-test"
    Then I wait for ".title"

    And I should see "Text with markup allowed." in the ".text_markup_a" element
    And I should see "Text with markup allowed." in the ".text_markup_b" element
    And I should see "Text with <em>markup allowed</em>." in the ".text_markup_c" element

    And I should see "Text with <strong>no</strong> markup allowed" in the ".text_plain_a" element
    And I should see "Text with no markup allowed" in the ".text_plain_b" element
    And I should see "Text with <strong>no</strong> markup allowed" in the ".text_plain_c" element

    And I should see "HTML field with simple HTML in it." in the ".text_html" element
    And I should see "Markdown field with simple Markdown in it." in the ".text_markdown" element
    And I should see "Textarea field with <em>simple</em> HTML in it." in the ".text_textarea" element

    And I should see "Text field with <strong>markup</strong>, including <script>console.log('hoi')</script>. The end." in the ".text_sanitise_a" element
    And I should see "Text field with <strong>markup</strong>, including . The end." in the ".text_sanitise_b" element

  @javascript
  Scenario: As a user I want to see how file fields are displayed
    Given I am on "/test/title-of-the-test"
    Then I wait for ".title"

    And I should see "joey.jpg" in the "#attachment #filename" element
    And I should see "/files/joey.jpg" in the "#attachment #path" element
    And I should see "attachment" in the "#attachment #fieldname" element
    And I should see "http" in the "#attachment #url" element
    And I should see "://" in the "#attachment #url" element
    And I should see "/files/joey.jpg" in the "#attachment #url" element
