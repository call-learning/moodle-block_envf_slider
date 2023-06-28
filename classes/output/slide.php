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
 * Contains class block_envf_slider\output\slide
 *
 * @package   block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_envf_slider\output;

use block_envf_slider;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class to represent a slide in the envf slider block.
 *
 * @package   block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class slide implements renderable, templatable {
    /** @var int $index The index of the slide. */
    protected $index;

    /** @var string $title The title of the slide. */
    protected $title;

    /** @var string $description The description of the slide. */
    protected $description;

    /** @var moodle_url $image The url of the background image of the slide. */
    protected $image;

    /** @var bool $whitetext A booleab telling whether the text has to be white. */
    protected $whitetext;

    /**
     * Constructor for a slide.
     *
     * @param int $index Index for the slide
     * @param string $title The title of the slide.
     * @param string $description The discription of the slide.
     * @param moodle_url|null $image The image url of the slide.
     * @param bool $whitetext whether the text has to be dsplayed in white or not.
     */
    public function __construct(int $index, string $title, string $description, ?moodle_url $image, bool $whitetext = false) {
        $this->index = $index;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->whitetext = $whitetext;
    }

    /**
     * Method to export data that will be used to render the slide template.
     *
     * @param renderer_base $output the renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'index' => $this->index,
            'title' => $this->title,
            'description' => $this->description,
            'whitetext' => $this->whitetext,
        ];
        $data['image'] = !empty($this->image) ? $this->image->out(true) : false;
        return $data;
    }
}
