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
    /**
     * Default height
     */
    const DEFAULT_HEIGHT = 350;

    /**
     * Default timer autoplay
     */
    const DEFAULT_TIMER_AUTOPLAY = 5000;

    /** @var array $slides An array of {@see slide} representing the slides the block contains. */
    private $slides;

    /**
     * @var int $maxheight maximum height for the slider
     */
    private $maxheight;
    /**
     * @var int $timer Time in microseconds to change slides
     */
    private $timer;

    /**
     * Constructor for {@see block}.
     *
     * @param array $slides An array of {@see slide} that the block contains.
     * @param int|null $maxheight Maximum height for the slider
     * @param int|null $timer Time between slides
     */
    public function __construct(array $slides, ?int $maxheight = null, ?int $timer = null) {
        $this->slides = $slides;
        $this->maxheight = $maxheight ?? self::DEFAULT_HEIGHT;
        $this->timer = $timer ?? self::DEFAULT_TIMER_AUTOPLAY;
    }

    /**
     * Method to export data that will be used to render the block template.
     *
     * @param renderer_base $output the renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $slides = [];
        foreach ($this->slides as $slide) {
            $slides[] = $slide->export_for_template($output);
        }
        return [
            'slides' => $slides,
            'maxheight' => $this->maxheight,
            'autoplay' => $this->timer
        ];
    }
}

