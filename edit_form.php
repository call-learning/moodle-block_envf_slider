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
use block_envf_slider\output\block;

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
     * Number of elements to repeat
     */
    const REPEAT_HIDDEN_ELEMENT = 'slides_repeats';

    /**
     * Set for data and retrieve images from config
     *
     * @param array|stdClass $defaults
     */
    public function set_data($defaults) {
        parent::set_data($defaults);
        $newdata = (object) [
            'config_count' => $defaults->config_count ?? 0,
        ];
        $optionalparamarray = optional_param_array('config_slide_delete', [], PARAM_RAW);
        if (count($optionalparamarray) > 0) {
            foreach (array_keys($optionalparamarray) as $slideindex) {
                foreach (array_keys($this->get_repeated_elements()) as $elementname) {
                    if ($elementname == 'slide_delete') {
                        continue;
                    }
                    $confignfieldame = 'config_' . $elementname;
                    $configvalues = $defaults->$confignfieldame;
                    unset($configvalues[$slideindex]);
                    $configvalues = array_values($configvalues);
                    $newdata->$confignfieldame = $configvalues;
                }
                $newdata->config_count--;
            }
        }
        // Restore filemanager fields.
        // This is a bit of a hack working around the issues of the block.
        // When using set_data, we set the file data to the real file as it reads it
        // from the block config,
        // not the draft manager file. This can be rectified by a second call to set_data.
        // We try to get the previously submitted file.
        if (!empty($this->block->config) && is_object($this->block->config)) {
            $numthumbnails = $newdata->config_count ?? 0;
            for ($index = 0; $index < $numthumbnails; $index++) {
                $imagefieldname = 'config_slide_image';
                $newdata->{$imagefieldname}[$index] = array();
                // Here we could try to use the file_get_submitted_draft_itemid, but it expects to have an itemid defined
                // Which is not what we have right now, we just have a flat list.
                $param = optional_param_array($imagefieldname, [], PARAM_INT);
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

                $newdata->{$imagefieldname}[$index] = $draftitemid;
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
        return [
            'slide_title' => [
                PARAM_TEXT,
                'text',
                get_string('config:slidetitle', 'block_envf_slider')
            ],
            'slide_description' => [
                PARAM_CLEANHTML,
                'editor',
                get_string('config:slidedescription', 'block_envf_slider')
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
     * Get usual options for filemanager
     *
     * @return array
     */
    protected function get_file_manager_options(): array {
        return array('subdirs' => false,
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
            $data->config_count = $data->{self::REPEAT_HIDDEN_ELEMENT} ?? 0;
        }
        if (!empty($data->config_slide_delete)) {
            foreach (array_keys($data->config_slide_delete) as $slideindex) {
                $this->delete_slide($slideindex, $data);
            }
        }
        return $data;
    }

    /**
     * Todo: complete phpdoc.
     *
     * @param int $slideindex
     * @param object $data
     * @return void
     */
    private function delete_slide(int $slideindex, object &$data) {
        $data->config_count--;
        foreach ($this->get_repeated_elements() as $key => $element) {
            if (isset(($data->{'config_' . $key})[$slideindex])) {
                unset(($data->{'config_' . $key})[$slideindex]);
                $data->{'config_' . $key} = array_values($data->{'config_' . $key});
            }
        }
    }

    /**
     * Form definition
     *
     * <pre>
     * "slide": {
     *      "title": "Title of the slide",
     *      "description": "Description of the slide",
     *      "image": imageID of the slide
     * }
     * </pre>
     *
     * @param MoodleQuickForm $mform The formulary used.
     * @return void
     * @throws coding_exception
     */
    protected function specific_definition($mform) {
        // Slide section.
        $repeatarray = [];
        $repeatedoptions = [];
        $mform->addElement('header', 'slides', get_string('slidesection', 'block_envf_slider'));

        foreach ($this->get_repeated_elements() as $key => $values) {
            $repeatedoptions["config_$key"]['type'] = array_shift($values);
            // Insert the name of the field in the second element.
            array_splice($values, 1, 0, "config_$key");
            $repeatarray[] = $mform->createElement(...$values);
        }
        $this->repeat_elements($repeatarray, $this->get_current_repeats(),
            $repeatedoptions,
            self::REPEAT_HIDDEN_ELEMENT,
            'slides_add_fields',
            1,
            get_string('config:addmoreslides', 'block_envf_slider'),
            true
        );
        $mform->addElement('header', 'general', get_string('general', 'block_envf_slider'));

        $mform->addElement('text',
            'config_maxheight',
            get_string('config:maxheight', 'block_envf_slider'),
            block::DEFAULT_HEIGHT
        );
        $mform->setDefault('config_maxheight', block::DEFAULT_HEIGHT);
        $mform->setType('config_maxheight', PARAM_INT);

        $mform->addElement('text',
            'config_timer',
            get_string('config:timer', 'block_envf_slider'),
            block::DEFAULT_TIMER_AUTOPLAY
        );
        $mform->setDefault('config_timer', block::DEFAULT_TIMER_AUTOPLAY);
        $mform->setType('config_timer', PARAM_INT);

    }

    /**
     * Get number of repeats
     */
    protected function get_current_repeats() {
        $repeats = $this->optional_param(
            self::REPEAT_HIDDEN_ELEMENT,
            $this->block->get_slides_count(),
            PARAM_INT
        );
        return $repeats;
    }
}
