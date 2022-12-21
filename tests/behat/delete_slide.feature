@block @block_envf_slider
Feature: Adding and deleting slides in ENVF slider block
  In order to manage Slides block used
  As a admin
  I need to delete some slides from my block.

  Background:
    Given I log in as "admin"
    And I turn editing mode on
    And I add the "ENVF Slider" block
    And I configure the "ENVF Slider" block

    And I press "Add more slides"
    And I set the field "Title slide 1" to "Test title 1"
    And I set the field "Description slide 1" to "Test description 1"
    And I upload "blocks/envf_slider/tests/fixtures/stonks.jpg" file to "Image slide 1" filemanager
    And I click on "White text for slide 1 ?" "checkbox"

    And I press "Add more slides"
    And I set the field "Title slide 2" to "Test title 2"
    And I set the field "Description slide 2" to "Test description 2"
    And I upload "blocks/envf_slider/tests/fixtures/phpstormlogo.png" file to "Image slide 2" filemanager
    And I press "Save changes"

  @javascript
  Scenario: Deleting the last slide, this should result in only one slide remaining and visible on the homepage.

    Given I configure the "ENVF Slider" block
    When I click on "config_slide_delete[1]" "button"

    Then I should see "Title slide 1"
    And I should see "Description slide 1"
    And I should see "White text for slide 1 ?"

    And I should not see "Title slide 2"
    And I should not see "Description slide 2"
    And I should not see "White text for slide 2 ?"

  @javascript @_file_upload
  Scenario: Deleting the first slide, this should result in only one slide remaining and visible on the homepage and the indexes of
  the second slide should be switched to 1.

    Given I configure the "ENVF Slider" block
    When I click on "config_slide_delete[0]" "button"

    Then I should see "Title slide 1"
    And I should see "Description slide 1"
    And I should see "White text for slide 1 ?"

    And I should not see "Title slide 2"
    And I should not see "Description slide 2"
    And I should not see "White text for slide 2 ?"

