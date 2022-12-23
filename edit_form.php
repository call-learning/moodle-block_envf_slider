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
            $optionalparamarray = optional_param_array('config_slide_delete', [], PARAM_RAW);
            $optionalparam = array_shift($optionalparamarray);
            $slidetodelete = explode('°', $optionalparam)[1];
            $newfields = $this->delete_slide(intval($slidetodelete) - 1);
            moodleform::set_data($newfields);
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

            // Todo moodleform::getData returns wrong config values with 2ids and whitetext instead of 1.
            // To reproduce : create 2 slides, save changes & delete one slide.
            // Todo find another way to do this.
            // Force saving the configuration but this makes us unable to cancel or undo changes.
            $this->block->instance_config_save($this->block->config);
        }
    }

    /**
     * Get number of repeats
     */
    protected function get_current_repeats() {
        $idcount = empty($this->block->config->slide_id) ? 0 : count($this->block->config->slide_id);
        return $idcount;
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
        $this->add_slides_elements();
    }

    /**
     * A method that add slide elements to the form.
     *
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
    private function add_slides_elements() {
        $mform =& $this->mform;
        $repeatarray = array();
        $repeatedoptions = array();

        // Todo : fix slides ids always set to 0.
        $numberofslides = $this->get_current_repeats();
        $repeatarray[] = $mform->createElement(
            'hidden',
            "config_slide_id",
            $numberofslides
        );
        $repeatedoptions['config_slide_id']['type'] = PARAM_INT;

        $repeatarray[] = $mform->createElement('text', 'config_slide_title',
            get_string('config:slidetitle', 'block_envf_slider'));
        $repeatedoptions['config_slide_title']['type'] = PARAM_TEXT;

        $repeatarray[] = $mform->createElement('text', 'config_slide_description',
            get_string('config:slidedescription', 'block_envf_slider'));
        $repeatedoptions['config_slide_description']['type'] = PARAM_TEXT;

        $repeatarray[] = $mform->createElement(
            'advcheckbox',
            'config_slide_whitetext',
            get_string('config:whitetext', 'block_envf_slider'),
            '',
            null,
            [false, true]
        );
        $repeatedoptions['config_slide_whitetext']['type'] = PARAM_BOOL;

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

    /**
     * Todo: complete phpdoc.
     *
     * @param int $slideindex
     * @return array
     */
    private function delete_slide($slideindex): array {
        $mform =& $this->mform;
        if (is_int($slideindex)) {
            if (isset($this->block->config->slide_id[$slideindex])) {
                $slidenumber = $this->get_current_repeats();
                for ($i = $slideindex + 1; $i < $slidenumber; $i++) {
                    // Setting new id values for all the slides that comes after the one we're deleting.
                    $newid = $this->block->config->slide_id[$i] - 1;
                    $this->block->config->slide_id[$i] = $newid;
                    $idelement = $mform->getElement("config_slide_id[$i]");
                    $idelement->setValue($newid);
                }
                foreach ($this->block->config as $configfieldname => $configfieldvalue) {
                    if (preg_match('/^slide_\S+/', $configfieldname)) {
                        $elementname = "config_{$configfieldname}[$slideindex]";
                        $mform->removeElement($elementname);

                        // TODO during the loop in formslib::exportValues,
                        // $this->_elements contains values as set as we didn't deleted the slide.
                        // Even with these 3 unset calls and the previous removeElement call.
                        unset($mform->_elements[$mform->_elementIndex[$elementname]]);
                        unset($mform->_elementIndex[$elementname]);
                        unset($this->block->config->{$configfieldname}[$slideindex]);
                    }
                }
                $mform->removeElement("config_slide_delete[$slideindex]");
                $mform->getElement("slides_repeats")->setValue($slidenumber - 1);
            }
            $mform->_elements = array_values($mform->_elements);
        } else {
            debugging("Warning ! Wrong value '$slideindex' passed to slideindex in block_envf_slider::delete_slide(int) method.");
        }
        // Reindex and submit to the form.
        return [
            'config_slide_id' => array_values($this->block->config->slide_id),
            'config_slide_title' => array_values($this->block->config->slide_title),
            'config_slide_description' => array_values($this->block->config->slide_description),
            'config_slide_image' => array_values($this->block->config->slide_image),
        ];
    }
}
