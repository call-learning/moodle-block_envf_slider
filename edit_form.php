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
        $newdata = new stdClass();
        $optionalparamarray = optional_param_array('config_slide_delete', [], PARAM_RAW);
        if (count($optionalparamarray) > 0) {
            foreach (array_keys($optionalparamarray) as $slideindex) {
                foreach (array_keys($this->get_repeated_elements()) as $elementname) {
                    if ($elementname == 'slide_delete') {
                        continue;
                    }
                    $configvalues = $defaults->{'config_' . $elementname};
                    $newdata->{'config_' . $elementname} = $defaults->{'config_' . $elementname};
                    unset($configvalues[$slideindex]);
                    $configvalues = array_values($configvalues);
                    $newdata->{'config_' . $elementname} = $configvalues;
                }
            }
        }
        // Restore filemanager fields.
        // This is a bit of a hack working around the issues of the block.
        // When using set_data, we set the file data to the real file as it reads it
        // from the block config,
        // not the draft manager file. This can be rectified by a second call to set_data.
        // We try to get the previously submitted file.
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $numthumbnails = $this->get_current_repeats();
            for ($index = 0; $index < $numthumbnails; $index++) {
                $fieldname = 'config_slide_image';
                $newdata->{$fieldname}[$index] = array();
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

                $newdata->{$fieldname}[$index] = $draftitemid;
            }
        }
        moodleform::set_data($newdata);
    }

    /**
     * Get all repeated elements to add to the form
     *
     * @return array[]
     * @throws coding_exception
     */
    private function get_repeated_elements() {
        $numberofslides = $this->get_current_repeats();
        return [
            'slide_id' => [
                PARAM_INT,
                'hidden',
                "config_slide_id",
                $numberofslides
            ],
            'slide_title' => [
                PARAM_TEXT,
                'text',
                'config_slide_title',
                get_string('config:slidetitle', 'block_envf_slider')
            ],
            'slide_description' => [
                PARAM_TEXT,
                'text',
                get_string('config:slidetitle', 'block_envf_slider')
            ],
            'slide_whitetext' => [
                PARAM_BOOL,
                'advcheckbox',
                get_string('config:whitetext', 'block_envf_slider'),
                '',
                null
            ],
            'slide_image' => [
                PARAM_RAW,
                'filemanager',
                get_string('config:slideimage', 'block_envf_slider'),
                null,
                $this->get_file_manager_options()
            ],
            'slide_delete' => [
                PARAM_RAW,
                'submit',
                get_string('config:deleteslide', 'block_envf_slider')
            ]

        ];
    }

    /**
     * Get number of repeats
     */
    protected function get_current_repeats() {
        $repeats = $this->optional_param(
            "slides_repeats",
            isset($this->block->config->slide_id) ? count($this->block->config->slide_id) : 0,
            PARAM_INT
        );
        return $repeats;
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
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();
        if (!empty($data)) {
            foreach (array_keys($data->config_slide_delete) as $slideindex) {
                foreach (array_keys($this->get_repeated_elements()) as $elementname) {
                    if ($elementname == 'slide_delete') {
                        continue;
                    }
                    unset($data->{'config_' . $elementname}[$slideindex]);
                    $data->{'config_' . $elementname} = array_values($data->{'config_' . $elementname});
                }
            }
        }
        return $data;
    }

    /**
     * Form definition
     *
     * @param MoodleQuickForm $mform The formulary used.
     * @throws coding_exception
     */
    protected function specific_definition($mform) {
        $this->mform =& $mform;
        $mform->createElement('hidden', 'slides_repeat', $this->get_current_repeats());
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
     *
     * @return void
     */
    private function add_slides_elements() {
        $repeatarray = [];
        $repeatedoptions = [];

        foreach ($this->get_repeated_elements() as $key => $values) {
            $repeatedoptions["config_$key"]['type'] = array_shift($values);
            array_splice($values, 1, 0, "config_$key");
            $repeatarray[] = $this->mform->createElement(...$values);
        }
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
        $return = [];
        if ($this->block->config && block_envf_slider::config_is_valid($this->block->config)) {
            $numberofslides = $this->get_current_repeats();
            for ($i = $slideindex + 1; $i < $numberofslides; $i++) {
                // Setting new id values for all the slides that comes after the one we're deleting.
                // In block's config.
                $newid = $this->block->config->slide_id[$i] - 1;
                $this->block->config->slide_id[$i] = $newid;
                // In the moodle form.
                $idelement = $this->mform->getElement("config_slide_id[$i]");
                $idelement->setValue($newid);
            }
            foreach ($this->block->config as $configfieldname => $configfieldvalue) {
                unset($this->block->config->{$configfieldname}[$slideindex]);
                if (preg_match('/^slide_\S+/', $configfieldname)) {
                    $elementname = "config_{$configfieldname}[$slideindex]";
                    $deletedelt = $this->mform->removeElement($elementname);
                }
            }
            $this->mform->removeElement("config_slide_delete[$slideindex]");
        }
        $this->mform->getElement("slides_repeats")->setValue($numberofslides - 1);
        return $return;
    }
}
