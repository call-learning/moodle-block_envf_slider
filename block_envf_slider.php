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

use block_envf_slider\output\block;
use block_envf_slider\output\slide;

/**
 * Class block_envf_slider
 *
 * @package    block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_envf_slider extends block_base {

    /**
     * Method to initialise block's values.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_envf_slider');

        // Initialise content.
        $this->content = (object) [
            'text' => ''
        ];
    }

    /**
     * Creates and returns all the content of the block.
     */
    public function get_content() {

        if ($this->content != null && !empty($this->content->text)) {
            return $this->content;
        }

        $this->page->requires->css(
            new moodle_url('/blocks/envf_slider/js/glide/dist/css/glide.core' .
                (debugging() ? '.min' : '') . '.css'));

        if (!$this->config_is_valid()) {
            $this->content->text = get_string("invalidconfig", "block_rss_thumbnails");
            return $this->content;
        }

        $renderer = $this->page->get_renderer('core');

        // Todo get a way to retrieve configured slides.
        $slides = [
            new slide(
                0,
                "My First Slide",
                "Helloooo this is my first slide ever created!",
                new moodle_url("https://cdn.pixabay.com/photo/2022/11/20/09/58/leaves-7603946_960_720.jpg"),
            ),
            new slide(
                1,
                "My Second Slide",
                "Second here !!",
                new moodle_url("https://cdn.pixabay.com/photo/2022/11/20/09/58/leaves-7603946_960_720.jpg"),
            ),
        ];

        $block = new block($slides);

        $this->content->text = $renderer->render($block);
        return $this->content;
    }

    /**
     * Checks wether the configuration of the block is valid or not.
     *
     * @return bool true if the configuration of the block is valid, false if it's not.
     */
    public function config_is_valid(): bool {
        // TODO implement config_is_valid function.
        return true;
    }

    /**
     * Serialize and store config data
     *
     * @param stdClass $data
     * @param false $nolongerused
     * @throws coding_exception
     */
    public function instance_config_save($data, $nolongerused = false) {
        $config = clone($data);
        // Save the images.
        if ($config->slide_title) {
            foreach ($config->slide_image as $index => $images) {
                file_save_draft_area_files($images,
                    $this->context->id,
                    'block_envf_slider',
                    'images',
                    $index,
                    array('subdirs' => true));
            }
            // Here we make sure we copy the image id into the
            // block parameter. This is then used in save_data
            // to setup the block to the right image.
            $fs = get_file_storage();
            $files = $fs->get_area_files($this->context->id,
                'block_envf_slider',
                'images'
            );
            foreach ($files as $file) {
                if (in_array($file->get_filename(), array('.', '..'))) {
                    continue;
                }
                $config->slide_image[$file->get_itemid()] = $file->get_id();
            }
        }
        parent::instance_config_save($config, $nolongerused);
    }

    /**
     * Delete the block and images.
     *
     * @return bool
     */
    public function instance_delete() {
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_envf_slider');
        return true;
    }

    /**
     * Copy any block-specific data when copying to a new block instance.
     *
     * @param int $fromid the id number of the block instance to copy from
     * @return boolean
     */
    public function instance_copy($fromid) {
        global $DB;

        $fromcontext = context_block::instance($fromid);
        $blockinstance = $DB->get_record('block_instances', array('id' => $fromcontext->instanceid));
        $block = block_instance($blockinstance->blockname, $blockinstance);
        $numslides = empty($block->config->slide_title) ? 0 : count($block->config->slide_title);

        $fs = get_file_storage();

        // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
        if (!$fs->is_area_empty($fromcontext->id, 'block_envf_slider', 'images', 0, false)) {
            for ($itemid = 0; $itemid < $numslides; $itemid++) {
                $draftitemid = 0;
                file_prepare_draft_area(
                    $draftitemid,
                    $fromcontext->id,
                    'block_envf_slider',
                    'images',
                    $itemid,
                    array('subdirs' => true));
                file_save_draft_area_files(
                    $draftitemid,
                    $this->context->id,
                    'block_envf_slider',
                    'images', $itemid,
                    array('subdirs' => true));
            }
        }
        return true;
    }
}
