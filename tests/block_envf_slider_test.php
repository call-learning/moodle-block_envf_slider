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

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the block_envf_slider class.
 *
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_envf_slider_test extends TestCase {

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
        // Todo implement test_config_is_valid method.
    }

    /**
     * Tests if the {@see block_envf_slider::get_configured_slides()} returns all the configured slides and well configured.
     *
     * @return void
     * @covers \block_envf_slider::get_configured_slides
     */
    public function test_get_configured_slides() {
        // Todo implement test_get_configured_slides method.
    }
}
