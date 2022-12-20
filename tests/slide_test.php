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
 * Base class for unit tests for block_envf_slider\output\slide.
 *
 * @package   block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_envf_slider;

use block_envf_slider\output\renderer;
use block_envf_slider\output\slide;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Unit tests for the block_envf_slider slides.
 *
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slide_test extends TestCase {

    /**
     * A method to initialize a slide with dummy properties.
     * The slide class being made the way that of its properties are in complete abstraction, we will use the
     * {@see slide::create_from_array()} method to create a slide with as property values the name of these properties.
     *
     * Note that all the properties get a string as value. We've made it this way to be compatible with older version than php 8.x.
     * When we'll start to type our properties, we'll have to use the {@see \ReflectionType} class to provide correct values to
     * the slide object.
     *
     * @return slide A slide object with dummy properties.
     */
    private function init_dummy_slide(): slide {
        $properties = array_keys(get_class_vars(slide::SLIDECLASSNAME));
        return slide::create_from_array($properties);
    }

    /**
     * Tests if the {@see slide::export_for_template()}} method returns the right data to be expoloit in the slide template.
     *
     * @return void
     * @covers \block_envf_slider\output\slide::export_for_template
     */
    public function test_export_for_template() {
        $slide = $this->init_dummy_slide();
        $renderer = new renderer();
        $data = $slide->export_for_template($renderer);
        foreach ($data as $key => $value) {
            self::assertTrue(property_exists($slide, $key));
            self::assertEquals(count(array_keys(get_class_vars($slide))), count($sata));
        }
    }

    /**
     * Tests if the {@see slide::create_from_array()} method works fine by creating a new slide element calling this method.
     *
     * @return void
     * @covers \block_envf_slider\output\slide::create_from_array
     */
    public function test_create_from_array() {
        $properties = array_keys(get_class_vars(slide::SLIDECLASSNAME));
        $slide = slide::create_from_array($properties);
        foreach ($properties as $property) {
            self::assertEquals($property, $slide->{$property});
        }
    }
}
