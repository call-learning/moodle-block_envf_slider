@block @block_envf_slider
Feature: Adding and configuring ENVF slider block
  In order to have Slides block used
  As a admin
  I need to add the ENVF slider block to the front page

  @javascript @_file_upload
  Scenario: Adding ENVF slider block and I add one slide, this should result in one slide editable on the edit form.

    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "ENVF Slider" block
    When I configure the "ENVF Slider" block
    And I press "Add more slides"
    Then I should see "Title slide 1"
    And I should see "Description slide 1"
    And I should see "White text for slide 1 ?"

  @javascript @_file_upload
  Scenario: Adding ENVF slider block and I add 2 slide and I configure them, this should result in 2 slides visible on the homepage.

    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on

    When I add the "ENVF Slider" block
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

    Then I should see "Test title 1" in the "ENVF Slider" block
    And I should see "Test title 2" in the "ENVF Slider" block
    And I should see "Test description 1" in the "ENVF Slider" block
    And I should see "Test description 2" in the "ENVF Slider" block
    And I should see the image "blocks/envf_slider/tests/fixtures/stonks.jpg" in the "ENVF Slider" block
    And I should see the image "phpstormlogo.png" in the "ENVF Slider" block
