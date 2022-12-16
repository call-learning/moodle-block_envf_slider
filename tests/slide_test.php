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

namespace block_envf_slider\output;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the block_envf_slider slides.
 *
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slide_test extends TestCase {

    /**
     * Tests if the {@see slide::export_for_template()}} method returns the right data to be expoloit in the slide template.
     *
     * @return void
     * @covers \block_envf_slider\output\slide::export_for_template
     */
    public function test_export_for_template() {
        // Todo implement this method.
    }

    /**
     * Tests if the {@see slide::create_from_array()} method works fine by creating a new slide element calling this method.
     *
     * @return void
     * @covers \block_envf_slider\output\slide::create_from_array
     */
    public function test_create_from_array() {
        // Todo implement this method.
    }
}
