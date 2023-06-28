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

use base_testcase;
use block_envf_slider\output\renderer;
use block_envf_slider\output\slide;

/**
 * Unit tests for the block_envf_slider slides.
 *
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slide_test extends base_testcase {

    /**
     * Tests if the {@see slide::export_for_template()}} method returns the right data to be expoloit in the slide template.
     *
     * @return void
     * @covers \block_envf_slider\output\slide::export_for_template
     */
    public function test_export_for_template() {

        $slide = new slide(0, '', '', new \moodle_url('/'));
        $renderer = new renderer(new \moodle_page(), null);
        $data = $slide->export_for_template($renderer);

        $this->assertEquals([
            'index' => 0,
            'title' => '',
            'description' => '',
            'whitetext' => false,
            'image' => 'https://www.example.com/moodle/',
        ], $data);
    }
}
