@block @block_envf_slider
Feature: Adding and configuring ENVF slider block
  In order to have Slides block used
  As a admin
  I need to add the ENVF slider block to the front page

  @javascript @_file_upload
  Scenario: Adding ENVF slider block and I add two slides then i come back and delete one slide, this should result in no slide visible on the homepage.

    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "ENVF Slider" block
    And I configure the "ENVF Slider" block
    When I press "Add more slides"
    Then I should see "Title slide 1"
    And I should see "Description slide 1"
    And I should see "White text for slide 1 ?"

    Given I set the field "Title slide 1" to "Test title"
    And I set the field "Description slide 1" to "Test description"
    And I upload "blocks/envf_slider/tests/fixtures/stonks.jpg" file to "Image slide 1" filemanager
    And I click on "White text for slide 1 ?" "checkbox"
    When I press "Add more slides"
    Then I should see "Title slide 2"
    And I should see "Description slide 2"
    And I should see "White text for slide 2 ?"

    Given I set the field "Title slide 2" to "Test title 2"
    And I set the field "Description slide 1" to "Test description 2"
    And I upload "blocks/envf_slider/tests/fixtures/stonks.jpg" file to "Image slide 2" filemanager
    When I press "Save changes"
    Then I should see "Test title"
    And I should see "Test title 2"
    And I should see "Test description"
    And I should see "Test description 2"

    Given I turn editing mode on
    And I configure the "ENVF Slider" block
    When I click on "config_slide_delete[0]" "button"
    Then I should not see "Title slide 2"
    And I should not see "Description slide 2"
    And I should not see "White text for slide 2 ?"