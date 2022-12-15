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

use block_envf_slider\form\delete_slide_form;

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
    private $mform;

    /**
     * Set for data and retrieve images from config
     *
     * @param array|stdClass $defaults
     */
    public function set_data($defaults) {
        parent::set_data($defaults);
        if ($this->no_submit_button_pressed()) {
            $slidetodelete = optional_param_array('config_slide_delete', [], PARAM_RAW);
            if (!empty($slidetodelete)) {
                foreach (array_keys($slidetodelete) as $slideindex) {
                    if (isset($this->block->config->slide_title[$slideindex])) {
                        unset($this->block->config->slide_title[$slideindex]);
                        unset($this->block->config->slide_description[$slideindex]);
                        unset($this->block->config->slide_image[$slideindex]);
                    }
                }
                // Reindex and submit to the form.
                $fields = [
                    'config_slide_title' => array_values($this->block->config->slide_title),
                    'config_slide_description' => array_values($this->block->config->slide_description),
                    'config_slide_image' => array_values($this->block->config->slide_image),
                ];
                moodleform::set_data($fields);
            }
        }
        // Restore filemanager fields.
        // This is a bit of a hack working around the issues of the block.
        // When using set_data, we set the file data to the real file as it reads it
        // from the block config,
        // not the draft manager file. This can be rectified by a second call to set_data.
        // We try to get the previously submitted file.
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $filefields = new stdClass();
            $numthumbnails = $this->get_current_repeats();
            for ($index = 0; $index < $numthumbnails; $index++) {
                $fieldname = 'config_slide_image';
                $filefields->{$fieldname}[$index] = array();
                // Here we could try to use the file_get_submitted_draft_itemid, but it expects to have an itemid defined
                // Which is not what we have right now, we just have a flat list.
                $param = optional_param_array($fieldname, [], PARAM_INT);
                if (!empty($param[$index])) {
                    $draftitemid = $param[$index];
                } else {
                    $draftitemid = 0;
                }
                file_prepare_draft_area($draftitemid,
                    $this->block->context->id,
                    'block_envf_slider',
                    'images',
                    $index,
                    $this->get_file_manager_options());

                $filefields->{$fieldname}[$index] = $draftitemid;
            }
            moodleform::set_data($filefields);
        }
    }

    /**
     * Checks if button pressed is not for submitting the form
     *
     * This is an override of the base method to ensure that array will also be processsed
     *
     * @staticvar bool $nosubmit keeps track of no submit button
     * @return bool
     */
    function no_submit_button_pressed() {
        foreach ($this->mform->_noSubmitButtons as $nosubmitbutton) {
            if (optional_param_array($nosubmitbutton, null, PARAM_RAW)) {
                return true;
            }
        }
        return parent::no_submit_button_pressed();
    }

    /**
     * Get number of repeats
     */
    protected function get_current_repeats() {
        $titlecount = empty($this->block->config->slide_title) ? 0 : count($this->block->config->slide_title);
        return $titlecount;
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
     * Form definition
     *
     * @param MoodleQuickForm $mform The formulary used.
     * @throws coding_exception
     */
    protected function specific_definition($mform) {
        $this->mform = $mform;

        // Gets all the slides previously added.
        $slides = $this->get_current_slides();
        $this->add_slides_elements($slides);
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
     * A method that add slide elements to the form.
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
    private function add_slides_elements($slide) {
        $mform = $this->mform;
        $repeatarray = array();
        $repeatedoptions = array();

        $repeatarray[] = $mform->createElement('text', 'config_slide_title',
            get_string('config:slidetitle', 'block_envf_slider'));
        $repeatedoptions['config_slide_title']['type'] = PARAM_TEXT;

        $repeatarray[] = $mform->createElement('text', 'config_slide_description',
            get_string('config:slidedescription', 'block_envf_slider'));
        $repeatedoptions['config_slide_description']['type'] = PARAM_TEXT;

        $repeatarray[] = $mform->createElement(
            'filemanager',
            'config_slide_image',
            get_string('config:slideimage', 'block_envf_slider'),
            null,
            $this->get_file_manager_options()
        );
        $repeatedoptions['config_slide_image']['type'] = PARAM_RAW;

        // The Delete Slide button.
        $repeatarray[] = $mform->createElement('submit', 'config_slide_delete',
            get_string('config:deleteslide', 'block_envf_slider'));
        $repeatedoptions['config_slide_delete']['type'] = PARAM_RAW;
        $this->mform->registerNoSubmitButton("config_slide_delete");
        $this->repeat_elements($repeatarray, $this->get_current_repeats(),
            $repeatedoptions,
            'slides_repeats',
            'slides_add_fields',
            1,
            get_string('config:addmoreslides', 'block_envf_slider'),
            true
        );

    }
}
