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
 * Edit Form
 *
 * @package    block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_envf_slider\delete_slide_form;

/**
 * Class block_envf_slider_edit_form
 *
 *
 * @package    block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_envf_slider_edit_form extends block_edit_form {

    /**
     * @var MoodleQuickForm $mform the formulary object passed in {@see block_envf_slider_edit_form::specific_definition()}
     * to be able to get it int the method {@see block_envf_slider_edit_form::add_slide()}
     */
    private MoodleQuickForm $mform;

    /**
     * Form definition
     *
     * @param $mform
     * @throws coding_exception
     */
    protected function specific_definition($mform) {
        $this->mform = $mform;
        $this->amd_requires();

        // Gets all the slides previously added.
        $slides = $this->get_current_slides();
        foreach ($slides as $slide) {
            $this->display_slide($slide);
        }

        // Button to ad a new slide.
        $addnewslidebtnname = 'addnewslide';
        $mform->addElement('submit', $addnewslidebtnname, get_string("config:addnewslide"));
        $mform->registerNoSubmitButton($addnewslidebtnname);
    }

    /**
     * Whenever a no submit button is pressed ( this could happen just with the addslide button), the form will add a new slide.
     *
     * @return void
     */
    public function no_submit_button_pressed() {
        $this->add_slide();
    }

    /**
     * Adds a new slide into the edit form of the block.
     *
     * @return stdClass an object containing the different fields to configure the slide
     */
    private function add_slide() {
        $slide = new stdClass();

        // Slide id.
        $id = $this->get_slide_count();
        $slide->id = $this->mform->addElement(
            'hidden',
            'config_slideid',
            $id
        );
        $slide->deleteform = new delete_slide_form($id);

        // Slide Title.
        $slide->title = $this->mform->addElement(
            'text',
            'config_slidetitle',
            get_string('config:slidetitle', 'block_envf_slider')
        );
        $this->mform->setType('config_slidetitle', PARAM_TEXT);

        // Slide desctiption.
        $slide->description = $this->mform->addElement(
            'textarea',
            'config_slidedescription',
            get_string('config:slidedescription', 'block_envf_slider')
        );
        $this->mform->setType('config:slidedescription', PARAM_TEXT);

        // Slide background image.
        $slide->image = $this->mform->addElement(
            'filemanager',
            'config_thumbimage',
            get_string('config:thumbimage', 'block_thumblinks_action')
        );

        // Button to remove a slide.
        $slide->removebtn = $this->mform->addElement(
            'submit',
            'remove_slide',
            get_string('config:removeslide')
        );
        return $slide;
    }

    /**
     * Calls all the amd fuctions we need.
     *
     * @return void
     */
    private function amd_requires() {
        $this->page->requires->js_call_amd('block_envf_slider/editform', 'calladdnewslide');
    }

    /**
     * Gets all the slides previously added.
     *
     * @return array an array containing a stdclass for each slide under this format :
     * <pre>
     * "slide": {
     *      "id": 0,
     *      "title": "Title of the slide",
     *      "description": "Description of the slide",
     *      "image": "image of the slide"
     * }
     * </pre>
     *
     */
    private function get_current_slides(): array {
        // Todo implement get_current_slides() and complete it's php doc.
        return [];
    }

    /**
     * Get number of repeats.
     *
     * @return int the number of repeats.
     */
    protected function get_slide_count(): int {
        return empty($this->block->config->slidetitle) ? 0 : count($this->block->config->slidetitle);
    }

    /**
     * Get usual options for filemanager
     *
     * @return array
     */
    protected function get_file_manager_options(): array {
        return array('subdirs' => 0,
            'maxbytes' => FILE_AREA_MAX_BYTES_UNLIMITED,
            'maxfiles' => 1,
            'context' => $this->block->context);
    }

    /**
     * A method that displays a fiven exisitng slide by adding to the formulary.
     *
     * @param stdClass $slide a slide under the form given by {@see block_envf_slider_edit_form::get_current_slides()}:
     * <pre>
     * "slide": {
     *      "id": 0,
     *      "title": "Title of the slide",
     *      "description": "Description of the slide",
     *      "image": "image of the slide"
     * }
     * </pre>
     * @return void
     */
    private function display_slide($slide) {
        // Todo implement displau_slide method
    }

}
