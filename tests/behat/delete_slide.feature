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

  @javascript @_file_upload
  Scenario Outline: Deleting the last slide, this should result in only one slide remaining and visible on the homepage.

    Given I press "Add more slides"
    And I set the field "Title slide 1" to "Test title 1"
    And I set the field "Description slide 1" to "Test description 1"
    And I upload "blocks/envf_slider/tests/fixtures/stonks.jpg" file to "Image slide 1" filemanager

    And I press "Add more slides"
    And I set the field "Title slide 2" to "Test title 2"
    And I set the field "Description slide 2" to "Test description 2"
    And I upload "blocks/envf_slider/tests/fixtures/phpstormlogo.png" file to "Image slide 2" filemanager

    When I click on "<delete_slide_button_name>" "button"

    Then I should see "Title slide 1"
    And I should see "Description slide 1"
    And I should see "White text for slide 1 ?"

    And I should not see "Title slide 2"
    And I should not see "Description slide 2"
    And I should not see "White text for slide 2 ?"

    And the field "Title slide 1" should be set to "Test title <remaining_slide_number>"
    And the field "Description slide 1" should be set to "Test description <remaining_slide_number>"

    Examples:
    | delete_slide_button_name | remaining_slide_number |
    | config_slide_delete[1]   | 1                      |
    | config_slide_delete[0]   | 2                      |