@block @block_envf_slider
Feature: Adding and deleting slides in ENVF slider block
  In order to manage Slides block used
  As a admin
  I need to delete some slides from my block.

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "ENVF Slider" block
    And I configure the "ENVF Slider" block

  @javascript @_file_upload
  Scenario Outline: Deleting the last slide, this should result in only one slide remaining and visible on the homepage.

    Given I press "Add more slides"
    And I set the field "Title slide 1" to "Test title 1"
    And I set the field "Description slide 1" to "Test description 1"
    And I upload "blocks/envf_slider/tests/fixtures/pexels-tom-dubois-17088081.jpg" file to "Image slide 1" filemanager

    And I press "Add more slides"
    And I set the field "Title slide 2" to "Test title 2"
    And I set the field "Description slide 2" to "Test description 2"
    And I upload "blocks/envf_slider/tests/fixtures/openclipart-342997.png" file to "Image slide 2" filemanager
    And I press "Save changes"

    Then I configure the "ENVF Slider" block
    And I click on "<delete_slide_button_name>" "button"

    Then I should see "Test title <remaining_slide_number>" in the "ENVF Slider" "block"
    And I should see "Test description <remaining_slide_number>" in the "ENVF Slider" "block"

    Then I configure the "ENVF Slider" block
    And the field "Title slide 1" should be set to "Test title <remaining_slide_number>"
    And the field "Description slide 1" should be set to "Test description <remaining_slide_number>"

    Examples:
      | delete_slide_button_name | remaining_slide_number |
      | Delete slide 2           | 1                      |
      | Delete slide 1           | 2                      |
