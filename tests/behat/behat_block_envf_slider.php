<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * File containing a class with additionnal step definitions for the ENVF slider block..
 *
 * @package     block_envf_slider
 * @copyright   2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Martin CORNU-MANSUY <martin@call-learning>
 */

use Behat\Mink\Element\NodeElement;

/**
 *  Behat customisations for the block
 *
 * @package     block_envf_slider
 * @copyright   2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author      Martin CORNU-MANSUY <martin@call-learning>
 */
class behat_block_envf_slider extends behat_base {

    /**
     * Search for a certain text displaying in the specified block.
     * The block is specified by its name.
     *
     * In order to search it, we use {@see \Behat\Mink\Element\Element::getText()} method to get all the text that a block contains
     * and {@see strpos()} method to find the researched text inside.
     *
     * @param string $text the text to search in the block.
     * @param string $blocktitle The title of the block in wich we want to find the text.
     * @throws Exception If the text is not found. Note that the {@see behat_block_envf_slider::get_block_by_title()} method will
     * also throw an exception if the specified block doesn't exist.
     * @Then I should see :text in the :blocktitle block
     */
    public function i_should_see_in_the_block($text, $blocktitle) {
        // Find the block element using its title.
        $blockelement = $this->get_block_by_title($blocktitle);

        // Get all the text inside the block.
        $blockhtml = $blockelement->getHtml();

        if (!strpos($blockhtml, $text)) {
            throw new Exception("Text '$text' not found in the block with title '$blocktitle'.");
        }
    }

    /**
     * Checks the value of a given field.
     *
     * @Then the field :field should be set to :value
     * @param string $field the field we want to check the value.
     * @param string $value the value we want the field to be set to.
     */
    public function the_field_should_be_set_to($field, $value) {
        $this->assertSession()->fieldValueEquals($field, $value);
    }

    /**
     * Checks wether an image is displayed into an envf slider block.
     *
     * @Then I should see the image :image in the :blocktitle envf slider block
     * @param string $image the path of the image that we want to check if it is displayed.
     * @param string $blocktitle the title of the EVNF slider block in wich we will search using the
     * {@see self::get_block_by_title()} method.
     */
    public function i_should_see_the_image_in_the_block($image, $blocktitle) {
        // Find the block element using its title.
        $blockelement = $this->get_block_by_title($blocktitle);

        $patharray = explode("/", $image);
        $imagename = end($patharray);

        $slides = $blockelement->findAll('css', '.slide-content');
        if (empty($slides)) {
            throw new Exception("No slides where found in the block '$blocktitle'");
        }
        foreach ($slides as $slide) {
            // Get the value of the style attribute.
            $style = $slide->getAttribute('style');
            // Check if the style attribute contains the background-image property with the expected image file name as the value.
            if (strpos($style, $imagename)) {
                return; // Return to end the loop.
            }
        }
        throw new Exception("Image '$imagename' not found as the background image of any slide in block '$blocktitle'.");
    }

    /**
     * Find the block element using its title.
     *
     * @param string $title The title of the block.
     * @return NodeElement The block element.
     */
    protected function get_block_by_title($title) {
        // Get the block elements.
        $blockelements = $this->getSession()->getPage()->findAll('css', '.block');

        // Find the block element with the specified title.
        foreach ($blockelements as $blockelement) {
            // Find the block header element.
            $blocktitlelement = $blockelement->find('css', '.card-title');
            if ($blocktitlelement) {
                $blocktitle = $blocktitlelement->getText();
                if (strtolower($blocktitle) === strtolower($title)) {
                    return $blockelement;
                }
            }
        }
        throw new Exception("Block with title '$title' not found.");
    }
}
