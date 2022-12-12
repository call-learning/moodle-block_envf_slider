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

    /** @var int $id The id of the slide. */
    private $id;

    /** @var string $title The title of the slide. */
    private $title;

    /** @var string $description The description of the slide. */
    private $description;

    /** @var moodleurl $imageurl The url of the background image of the slide. */
    private $imageurl;

    /**
     * Constructor for a slide.
     *
     * @param $id
     * @param $title
     * @param $description
     * @param $imageurl
     */
    public function __construct($id, $title, $description, $imageurl) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->imageurl = $imageurl;
    }

    /**
     * @inheritDoc
     */
    public function export_for_template(renderer_base $output) {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "description" => $this->description,
            "imageurl" => $this->imageurl
        ];
    }
}