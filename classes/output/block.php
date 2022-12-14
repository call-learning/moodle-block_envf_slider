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
 * Contains class block_envf_slider\output\block
 *
 * @package   block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_envf_slider\output;

use renderable;
use renderer_base;
use templatable;

/**
 * Class to represent a ENVF slider block.
 *
 * @package   block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block implements renderable, templatable {

    /** @var array $slides An array of {@see slide} representing the slides the block contains. */
    private $slides;

    /**
     * @param $slides array An array of {@see slide} that the block contains.
     */
    public function __construct($slides) {
        $this->slides = $slides;
    }

    /**
     * @inheritDoc
     */
    public function export_for_template(renderer_base $output) {
        $slides = [];
        foreach ($this->slides as $slide) {
            $slides[] = $slide->export_for_template($output);
        }
        return [
            "slides" => $slides
        ];
    }
}
