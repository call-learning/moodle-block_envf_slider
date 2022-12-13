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
 * Envf Slider block implementation.
 *
 * @package    block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Class block_envf_slider
 *
 * @package    block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_envf_slider extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_envf_slider');

        // Initialise content.
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
    }

    public function get_content() {
        global $DB;

        if ($this->content != null && !empty($this->content->text)) {
            return $this->content;
        }
        $this->page->requires->css(
            new moodle_url('/blocks/envf_slider/js/glide/dist/css/glide.core' .
                (debugging() ? '.min' : '') . '.css'));
        return null;

        if (!$this->config_is_valid()) {
            $this->content->text = get_string("invalidconfig", "block_rss_thumbnails");
            return $this->content;
        }
    }

    /**
     * Checks wether the configuration of the block is valid or not.
     *
     * @return bool true if the configuration of the block is valid, false if it's not.
     */
    public function config_is_valid() {
        // TODO implement config_is_valid function.
        return true;
    }

}
