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

use moodle_exception;
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

    /** @var int $id The id of the slide. */
    public $id;

    /** @var string $title The title of the slide. */
    public $title;

    /** @var string $description The description of the slide. */
    public $description;

    /** @var moodleurl $image The url of the background image of the slide. */
    public $image;

    /** @var bool $whitetext A booleab telling whether the text has to be white. */
    public $whitetext;

    /**
     * Constructor for a slide.
     *
     * @param int $id
     * @param string $title
     * @param string $description
     * @param moodle_url $imageurl
     * @param bool $whitetext
     */
    public function __construct($id, $title, $description, $image, $whitetext=false) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->whitetext = $whitetext;
    }

    /**
     * Creates a slide from an array of properties.
     *
     * @param $array
     * @return slide
     */
    public static function create_from_array($array): slide {
        if (count($array) == count(get_class_vars('slide'))) {
            throw new moodle_exception(
                "Error creating a slide from an array, expected ".count(get_class_vars('slide')).
                " values, got ".count($array)."."
            );
        }
        return new self(...$array);
    }

    /**
     * Method to export data that will be used to render the slide template.
     *
     * @param renderer_base $output the renderer.
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $data = [];
        foreach ($this as $attr => $value) {
            $data[$attr] = $value;
        }
        return $data;
    }
}
