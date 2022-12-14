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
 * Delete slide form file
 *
 * @package    block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_envf_slider\form;

use moodle_url;
use moodleform;

/**
 * Class delete_slide_form
 *
 * @package    block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_slide_form extends moodleform {

    /** @var int $slideid the id of the slide that will be deleted on submission of this form */
    private int $slideid;

    /**
     * Constructor of delete_slide_form
     *
     * @param int $slideid the id of the slide attached to this form (See {@see delete_slide_form::$slideid}).
     */
    public function __construct($slideid) {
        $this->slideid = $slideid;
    }

    /**
     * Gets the url to call to delete a particular slide.
     * @return moodle_url
     */
    private function get_action_url(): moodle_url {
        $urlparams = ["slideid" => $this->slideid];
        return new moodle_url("/blocks/envf_slider/delete_slide.php", $urlparams);
    }

    /**
     * Methods that defines the form.
     * It defines the form's components and their format, etc...
     */
    protected function definition() {
        parent::__construct(
            $this->get_action_url()
        );
        $this->_form->addElement(
            'submit',
            'deleteslidebtn',
            get_string('config:deleteslidebtn')
        );
    }
}
