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
 * Base class for unit tests for block_envf_slider.
 *
 * @package   block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_envf_slider;

use block_envf_slider;
use block_envf_slider\output\slide;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the block_envf_slider class.
 *
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_envf_slider_test extends TestCase {

    /**
     * @var block_envf_slider $block an object from the tested class to be used in the tests.
     * It is initialized in the {@see block_envf_slider_test::init()} method.
     */
    private block_envf_slider $block;

    /**
     * A method to initialize a block to be able to test properly all the {@see block_envf_slider}'s methods.
     *
     * @return void
     */
    public function setUp(): void {
        $this->block = new block_envf_slider();
    }

    /**
     * Tests if the {@see block_envf_slider::get_image_urls()} method returns valid  and useable image urls.
     *
     * @return void
     * @covers \block_envf_slider::get_image_urls
     */
    public function test_get_image_urls() {
        // Todo implement test_get_image_urls method.
    }

    /**
     * Tests if the {@see block_envf_slider::config_is_valid()} method returns well true if the block's configuration is valid,
     * and false if the block's configuration is not.
     *
     * @return void
     * @covers \block_envf_slider::config_is_valid
     */
    public function test_config_is_valid() {
        $properties = get_class_vars(slide::SLIDECLASSNAME);
        foreach ($properties as $property) {
            assertTrue(property_exists($this->block->config, block_envf_slider::get_config_property_name($property)));
        }
    }

    /**
     * Tests if the {@see block_envf_slider::get_configured_slides()} returns all the configured slides and well configured.
     *
     * @return void
     * @covers \block_envf_slider::get_configured_slides
     */
    public function test_get_configured_slides() {
        $numslides = $this->block->get_number_of_items();
        $configuredslides = $this->block->get_configured_slides();
        self::assertEquals($numslides, count($configuredslides));
    }
}
