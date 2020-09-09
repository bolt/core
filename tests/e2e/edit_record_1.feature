Feature: Edit record
  @javascript
  Scenario: As an Admin I want to change title of a record
    Given I am logged in as "admin"
    When I am on "/bolt/edit/30"

    When I fill in "field-title" with "Changed title"
    And I scroll "Save changes" into view
    And I press "Save changes"

    When I am on "/bolt/edit/30"
    Then I should see "Changed title" in the ".admin__header--title" element

  @javascript
  Scenario: As an Admin I want to change title of a record in another language
    Given I am logged in as "admin"
    When I am on "/bolt/edit/1"

    When I fill in "field-title" with "Changed title EN"
    And I scroll "Save changes" into view
    And I press "Save changes"

    And I scroll "#multiselect-localeswitcher div.multiselect__select" into view
    Then I click "#multiselect-localeswitcher div.multiselect__content-wrapper > ul > li:nth-child(2) > span"

    When I fill in "field-title" with "Changed title NL"
    And I scroll "Save changes" into view
    And I press "Save changes"

    When I am on "/bolt/edit/1?edit_locale=nl"
    And I wait for ".admin__header--title"
    Then the "field-title" field should contain "Changed title NL"

    When I am on "/bolt/edit/1?edit_locale=nl&_locale=nl"
    And I wait for ".admin__header--title"
    Then the "field-title" field should contain "Changed title NL"

    When I am on "/bolt/edit/1?edit_locale=en&_locale=nl"
    And I wait for ".admin__header--title"
    Then the "field-title" field should contain "Changed title EN"

    When I am on "/page/1?locale=nl"
    Then I should see "Changed title NL"

  @javascript
  Scenario: As an Admin I want to be able to make use of the embed field
    Given I am logged in as "admin"
    When I am on "/bolt/edit/44"
    Then I follow "Media"

    Then I fill in "fields[embed][url]" with "https://www.youtube.com/watch?v=x4IDM3ltTYo"
    And I wait for "fields[embed][title]" field value to change
    Then the "fields[embed][title]" field should contain "Silversun Pickups - Nightlight (Official Video)"
    And the "fields[embed][authorname]" field should contain "Silversun Pickups"
    And the "fields[embed][width]" field should contain "480"
    And the "fields[embed][height]" field should contain "270"

    When I click ".editor__embed .remove"
#   Add wait to make sure JS clears the fields before assert occurs
    And I wait 1 second
    And the "fields[embed][url]" field should contain ""
    And the "fields[embed][title]" field should contain ""
    And the "fields[embed][authorname]" field should contain ""
    And the "fields[embed][width]" field should contain ""
    And the "fields[embed][height]" field should contain ""

  @javascript
  Scenario: As an Admin I want to see the field infobox
    Given I am logged in as "admin"
    When I am on "/bolt/edit/38"

    Then I should see an "label[for='field-email']" element
    And I should see 1 "i" elements in the "label[for='field-email']" element

    When I scroll "Email" into view
    When I hover over the "label[for='field-email'] > i" element
    Then I should see "Email" in the ".popover-header" element
    And I should see "This is an info box shown as a popover next to the field label." in the ".popover-body" element

  @javascript
  Scenario: As an Admin I want to fill in an imagelist
    Given I am logged in as "admin"
    When I am on "/bolt/edit/42"
    Given I am logged in as "admin"
    And I am on "/bolt/edit/42"
    Then I follow "Media"
    Then I should see "Imagelist" in the "label[for='field-imagelist']" element

    When I scroll the 2nd "image-upload-dropdown" into view
    And I press the 2nd "image-upload-dropdown" button
    And I press the 2nd "From library" button
    And I wait until I see "Select a file"
    And I select "kitten.jpg" from "bootbox-input"
    And I press "OK"
    And I wait 1 second
    Then the "fields[imagelist][0][filename]" field should contain "kitten.jpg"
    And I fill "fields[imagelist][0][alt]" element with "Image of a kitten"

    When I press "Add new image"
    Then I should see 5 ".row" elements in the ".editor__imagelist" element

    When I scroll the 6th "image-upload-dropdown" into view
    And I press the 6th "image-upload-dropdown" button
    And I press the 6th "From library" button
    And I wait until I see "Select a file"
    And I select "joey.jpg" from "bootbox-input"
    And I press "OK"
    And I wait 1 second
    Then the "fields[imagelist][4][filename]" field should contain "joey.jpg"
    And I fill "fields[imagelist][4][alt]" element with "Image of a joey"

    When I scroll the 1st "Down" into view
    And I press the 1st "Down" button
    Then the "fields[imagelist][1][filename]" field should contain "kitten.jpg"
    And the "fields[imagelist][1][alt]" field should contain "Image of a kitten"

    When I press the 2nd "Up" button
    Then the "fields[imagelist][0][filename]" field should contain "kitten.jpg"
    And the "fields[imagelist][0][alt]" field should contain "Image of a kitten"

    And the 1st "Up" button should be disabled
    And the 5th "Down" button should be disabled

    When I press the 2nd "Remove" button
    Then I should see 4 ".row" elements in the ".editor__imagelist" element

    When I scroll "Save changes" into view
    And I press "Save changes"
    Then I should be on "/bolt/edit/42#media"
    And I should see 4 ".row" elements in the ".editor__imagelist" element

  @javascript
  Scenario: As an Admin I want to fill in a filelist
    Given I am logged in as "admin"
    And I am on "/bolt/edit/42"
    Given I am logged in as "admin"
    And I am on "/bolt/edit/42"
    When I follow "Files"
    Then I should see "Filelist" in the "label[for='field-filelist']" element

    When I scroll the 2nd "file-upload-dropdown" into view
    And I press the 2nd "file-upload-dropdown" button
    And I press the 7th "From library" button
    And I wait until I see "Select a file"
    And I select "bolt4.pdf" from "bootbox-input"
    And I press "OK"
    Then the "fields[filelist][0][filename]" field should contain "bolt4.pdf"
    And I wait 1 second

    When I press "Add new file"
    Then I should see 5 ".row" elements in the ".editor-filelist" element
    And the 1st "Add new file" button should be disabled

    When I scroll the 6th "file-upload-dropdown" into view
    And I press the 6th "file-upload-dropdown" button
    And I press the 11th "From library" button
    And I wait until I see "Select a file"
    And I select "joey.jpg" from "bootbox-input"
    And I press "OK"
    And I wait 1 second
    Then the "fields[filelist][4][filename]" field should contain "joey.jpg"

    When I scroll the 5th "Down" into view
    And I press the 5th "Down" button
    And I wait 1 second
    Then the "fields[filelist][1][filename]" field should contain "bolt4.pdf"

    When I press the 6th "Up" button
    Then the "fields[filelist][0][filename]" field should contain "bolt4.pdf"

    And the 5th "Up" button should be disabled
    And the 9th "Down" button should be disabled

    When I press the 7th "Remove" button
    Then I should see 4 ".row" elements in the ".editor-filelist" element
    And the 1st "Add new file" button should be enabled

    When I scroll "Save changes" into view
    And I press "Save changes"
    Then I should be on "/bolt/edit/42#files"
    And I should see 4 ".row" elements in the ".editor-filelist" element

  @javascript
  Scenario: As an Admin I want to fill in a Set
    Given I am logged in as "admin"
    And I am on "/bolt/edit/43"
    Then I should see "Sets" in the ".editor__tabbar" element

    When I follow "Sets"
    Then I should be on "/bolt/edit/43#sets"
    And I should see "Set" in the "#sets label[for='field-set']" element

    And I should see "Title" in the "#sets label[for='field-set-title']" element
    And I should see exactly one "sets[set][title]" element

    And I should see "Textarea" in the "#sets label[for='field-set-textarea']" element
    And I should see exactly one "sets[set][textarea]" element

    And I fill "sets[set][title]" element with "Foo"
    And I fill "sets[set][textarea]" element with "Bar"

    And I scroll "Save changes" into view
    And I press "Save changes"

    Then I should be on "/bolt/edit/43#sets"
    And the field "sets[set][title]" should contain "Foo"
    And the field "sets[set][textarea]" should contain "Bar"

  @javascript
  Scenario: As an Admin I want to fill in a Collection
    Given I am logged in as "admin"
    And I am on "/bolt/edit/43"
    Then I should see "Collections" in the ".editor__tabbar" element

    When I follow "Collections"
    Then I should be on "/bolt/edit/43#collections"
    And I should see "Collection:" in the "label[for='field-collection']" element

    #templates dropdown
    When I click "Add item to Collection"
    And I follow "Set"

    Then I should see "Set" in the "#multiselect-undefined li:nth-child(1) > span" element
    And the "#multiselect-undefined li:nth-child(2) > span" element should contain "Textarea"

    When I click "#multiselect-undefined > div > div.multiselect__select"
    And I click "#multiselect-undefined > div > div.multiselect__content-wrapper > ul > li:nth-child(2) > span"
    And I press "Add item"

    Then I should see an ".collection-item" element
    And I should see an ".trumbowyg-editor" element
#    And I should see "Textarea" in the "#collections label[for='field-collection-textarea-2']" element

    And the 1st ".action-move-up-collection-item" button should be disabled
    And the 3rd ".action-move-down-collection-item" button should be disabled

    When I scroll "Add item to Collection" into view
    And I click "Add item to Collection"
    And I follow "Textarea"

    Then I should see 4 ".collection-item" elements

    When I fill the 2nd "#collections textarea" element with "Bye, Bolt"
    And I fill the 2nd ".collection-item input[type='text']" element with "Hey, Bolt"

    And I scroll the 3rd "Move down" into view
    And I press the 3rd "Move down" button

    Then I should see "Set:" in the 3rd "#collections .collection-item" element
    And I should see "Textarea:" in the 3rd "#collections .collection-item label" element

    When I scroll "Save changes" into view
    And I press "Save changes"
    Then I should be on "/bolt/edit/43#collections"

    And the field ".collection-item:nth-child(4) input[type='text']" should contain "Hey, Bolt"
    And the field ".collection-item:nth-child(5) textarea" should contain "Bye, Bolt"

    When I scroll the 3rd "Remove item" into view
    And I press the 3rd "Remove item" button
    And I wait for ".modal-dialog"
    Then I should see "Are you sure you wish to delete this collection item?"
    And I press "OK"

    #4th becomes 3rd on prev removal
    And I press the 3rd "Remove item" button
    And I wait for ".modal-dialog"
    Then I should see "Are you sure you wish to delete this collection item?"
    And I press "OK"

    Then I should see 2 ".collection-item" elements

    When I scroll "Save changes" into view
    And I press "Save changes"

    Then I should see 2 ".collection-item" elements
    And I should not see "Hey, Bolt"
    And I should not see "Bye, Bolt"

  @javascript
  Scenario: As an Admin I want to see separated content (separator)
    Given I am logged in as "admin"
    And I am on "/bolt/edit/43"
    
    Then I should see 1 "hr" elements in the "#field-html-html" element

  @javascript
  Scenario: As an Admin I want to see placeholder on new content
    Given I am logged in as "admin"
    And I am on "/bolt/new/showcases"

    Then the "fields[title]" field should have "placeholder='Placeholder for the title'" attribute

  @javascript
  Scenario: As an Admin, I want to reset an image field
    Given I am logged in as "admin"
    And I am on "/bolt/edit/40"

    When I follow "Media"

    Then I should see "Image" in the "label[for=field-image]" element
    And the "fields[image][filename]" field should be filled in
    And the "fields[image][alt]" field should be filled in

    When I press the 1st "Remove" button
    Then the "fields[image][filename]" field should contain ""
    And the "fields[image][alt]" field should contain ""

  @javascript
  Scenario: As an Admin, I want to see default values on new content
    Given I am logged in as "admin"
    And I am on "/bolt"

    When I hover over the "Tests" element
    And I click the 5th "New"

    Then I should be on "/bolt/new/tests"

    And the "fields[title]" field should contain "Title of a test contenttype"
    And the "fields[image][filename]" field should contain "foal.jpg"

    And the "sets[set_field][title]" field should contain "This is the default title value"
    And the "sets[set_field][year]" field should contain "2020"

    And the "collections[collection_field][photo][1][filename]" field should contain "kitten.jpg"
    And the "collections[collection_field][photo][1][alt]" field should contain "Cute kitten"

    And the "collections[collection_field][paragraph][2]" field should contain "An image, followed by some content"

    And the "collections[collection_field][photo][3][filename]" field should contain "joey.jpg"
    And the "collections[collection_field][photo][3][alt]" field should contain "Photo of a foal"

    When I scroll "Save changes" into view
    And I press "Save changes"

    Then the "fields[title]" field should contain "Title of a test contenttype"
    And the "fields[image][filename]" field should contain "foal.jpg"

    And the "sets[set_field][title]" field should contain "This is the default title value"
    And the "sets[set_field][year]" field should contain "2020"

    And the "collections[collection_field][photo][1][filename]" field should contain "kitten.jpg"
    And the "collections[collection_field][photo][1][alt]" field should contain "Cute kitten"

    And the "collections[collection_field][paragraph][2]" field should contain "An image, followed by some content"

    And the "collections[collection_field][photo][3][filename]" field should contain "joey.jpg"
    And the "collections[collection_field][photo][3][alt]" field should contain "Photo of a foal"

  @javascript
  Scenario: As an Admin, I want to duplicate a page
    Given I am logged in as "admin"
    And I am on "/bolt/content/pages"
    Then I should see "This is a page"

    When I click the 1st ".edit-actions__dropdown-toggler"
    #click duplicate
    And I click the 1st "Duplicate Page"

    Then I should be on "/bolt/duplicate/2"
    And the "fields[heading]" field should contain "This is a page"
    And the "fields[slug]" field should contain "this-is-a-page"

    When I scroll "Save changes" into view
    And I click "Save changes"

    Then the "fields[heading]" field should contain "This is a page"
    And the "fields[slug]" field should contain "this-is-a-page-1"
