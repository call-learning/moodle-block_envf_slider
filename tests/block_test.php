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
 * Base class for unit tests for block_envf_slider\output\block.
 *
 * @package   block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_envf_slider;

use base_testcase;
use block_envf_slider\output\block;
use block_envf_slider\output\renderer;
use block_envf_slider\output\slide;
use moodle_page;

/**
 * Unit tests for the block_envf_slider's block.
 *
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_test extends base_testcase {

    /**
     * Tests if the {@see block::export_for_template()} method works well by checking if the value returned is exploitable
     * for the block's template
     *
     * @return void
     * @covers \block_envf_slider\output\block::export_for_template
     */
    public function test_export_for_template() {
        $slides = [];
        $maxindex = 5;
        for ($i = 0; $i < $maxindex; $i++) {
            $slides[] = new slide(0, '', '', new \moodle_url('/'));
        }
        $block = new block($slides);
        $data = $block->export_for_template(new renderer(new moodle_page(), null));
        $this->assertEquals($maxindex, count($data["slides"]));
    }
}
