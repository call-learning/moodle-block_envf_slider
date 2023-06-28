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
    }

    /**
     * Creates and returns all the content of the block.
     */
    public function get_content() {
        if ($this->content != null && !empty($this->content->text)) {
            return $this->content;
        }
        $this->content = new stdClass();
        if (!$this->config_is_valid()) {
            $this->content->text = get_string("invalidconfig", "block_envf_slider");
            return $this->content;
        }

        // Only add CSS to the page if the config is valid.
        $this->page->requires->css(
            new moodle_url('/blocks/envf_slider/js/glide/dist/css/glide.core' .
                (debugging() ? '.min' : '') . '.css')
        );
        $renderer = $this->page->get_renderer('core');

        $slides = $this->get_configured_slides();

        $block = new block($slides,
            $this->config->maxheight ?? null,
            $this->config->timer ?? null
        );

        $this->content->text = $renderer->render($block);
        return $this->content;
    }

    /**
     * Checks if the block's configuration is valid.
     *
     * @return bool True if the block's configuration is valide, false if not.
     */
    public function config_is_valid(): bool {
        // First check that config is not empey.
        if (empty($this->config)) {
            return false;
        }

        // Check if $this->config is an array or object.
        if (!is_array($this->config) && !is_object($this->config)) {
            return false;
        }

        if ($this->get_slides_count()) {
            for ($i = 0; $i < $this->get_slides_count(); $i++) {
                $slide = $this->get_slide_at($i);
                if (!$slide) {
                    // Something wrong here.
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * A method to get the correct number of configured slides.
     * This will count the number of slides in $this->config by counting the number of sildes id in it.
     * Note that the block's configuration ahs to be valid if we want a correct output.
     *
     * See {@see block_envf_slider::config_is_valid()}
     *
     * @return int|null
     */
    public function get_slides_count() {
        return $this->config->count ?? 0;
    }

    /**
     * An helper method to get the slide for the given position in the config
     *
     * @param int $position
     * @return slide|null An array of all the images moodle urls.
     */
    private function get_slide_at(int $position): ?slide {
        if ($this->get_slides_count() < $position) {
            return null;
        }
        $title = $this->config->slide_title[$position] ?? '';

        $image = null;
        $fs = get_file_storage();
        $allfiles = $fs->get_area_files($this->context->id, 'block_envf_slider', 'images', $position);
        if (isset($allfiles)) {
            foreach ($allfiles as $file) {
                if ($file->get_id() == $this->config->slide_image[$position]) {
                    $image = moodle_url::make_pluginfile_url(
                        $this->context->id,
                        'block_envf_slider',
                        'images',
                        $position,
                        $file->get_filepath(),
                        $file->get_filename()
                    );
                }
            }
        }
        $whitetext = $this->config->slide_whitetext[$position] ?? false;
        $descriptiontext = $this->config->slide_description[$position]['text'] ?? '';
        $descriptionformat = $this->config->slide_description[$position]['format'] ?? '';
        $description = format_text($descriptiontext, $descriptionformat, ['context' => $this->context]);
        return new slide($position, $title, $description, $image, $whitetext);
    }

    /**
     * Method that creates new {@see slide} objects from block's configuration and returns them into an array.
     *
     * @return array The array of already configured slides.
     */
    public function get_configured_slides(): array {
        if (!self::config_is_valid($this->config)) {
            throw new moodle_exception("invalidconfig", "block_envf_slider");
        }

        $slides = [];
        // Loop for each slide.
        for ($i = 0; $i < $this->get_slides_count(); $i++) {
            $slide = $this->get_slide_at($i);
            if ($slide) {
                $slides[] = $slide;
            }
        }
        return $slides;
    }

    /**
     * Serialize and store config data.
     *
     * @param stdClass $data
     * @param false $nolongerused
     * @throws coding_exception
     */
    public function instance_config_save($data, $nolongerused = false) {
        $config = clone($data);
        // Save the images.
        $fs = get_file_storage();
        if (!empty($config->slide_image)) {
            foreach ($config->slide_image as $index => $images) {
                file_save_draft_area_files($images,
                    $this->context->id,
                    'block_envf_slider',
                    'images',
                    $index,
                    array('subdirs' => false));
                // Replace the file by the right file id which should be the first image found in the area.
                $allfiles = $fs->get_area_files($this->context->id, 'block_envf_slider', 'images', $index);
                foreach ($allfiles as $f) {
                    if ($f->is_valid_image()) {
                        $config->slide_image[$index] = $f->get_id();
                    }
                }
            }
        }
        if (isset($data->slide_delete)) {
            unset($data->slide_delete);
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
                    array('subdirs' => false));
                file_save_draft_area_files(
                    $draftitemid,
                    $this->context->id,
                    'block_envf_slider',
                    'images',
                    $itemid,
                    array('subdirs' => false));
            }
        }
        return true;
    }

    /**
     * Hide header
     *
     * @return true
     */
    public function hide_header() {
        return true;
    }
}
