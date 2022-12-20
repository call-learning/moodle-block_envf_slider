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
                (debugging() ? '.min' : '') . '.css')
        );

        if (!$this->config_is_valid()) {
            $this->content->text = get_string("invalidconfig", "block_envf_slider");
            return $this->content;
        }

        $renderer = $this->page->get_renderer('core');

        $slides = $this->get_configured_slides();

        $block = new block($slides);

        $this->content->text = $renderer->render($block);
        return $this->content;
    }

    /**
     * Checks if the block's configuration is valid.
     *
     * @return bool True if the block's configuration is valide, false if not.
     */
    public function config_is_valid(): bool {
        // Check if $this->config is an array or object.
        if (!is_array($this->config) && !is_object($this->config)) {
            return false;
        }

        if (empty($this->config)) {
            return false;
        }

        // Use the get_class_vars() function to get the property names
        // of the Slide class.
        $propertynames = array_keys(get_class_vars(slide::SLIDECLASSNAME));

        // Check if all the property names have non-empty values.
        $numproperties = count($this->config->{"slide_$propertynames[0]"});

        foreach ($propertynames as $propertyname) {
            $configkey = self::get_config_property_name($propertyname);
            if (empty($this->config->$configkey)) {
                // Some config fields are missing.
                return false;
            }
            if (count($this->config->$configkey) !== $numproperties) {
                // Some slides are missing at least one config field.
                return false;
            }
        }
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
            // to set up the block to the right image.
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

    /**
     * Method that creates new {@see slide} objects from block's configuration and returns them into an array.
     *
     * @return array The array of already configured slides.
     */
    public function get_configured_slides(): array {
        if (!$this->config_is_valid()) {
            throw new moodle_exception("invalidconfig", "block_envf_slider");
        }

        $slides = [];
        $propertynames = array_keys(get_class_vars(slide::SLIDECLASSNAME));
        $imageurls = $this->get_image_urls();
        // Loop for each slide.
        $maxindex = count($this->config->{self::get_config_property_name($propertynames[0])});
        for ($i = 0; $i < $maxindex; $i++) {
            $array = [];
            foreach ($propertynames as $propertyname) {
                $array[$propertyname] = $this->config->{self::get_config_property_name($propertyname)}[$i];
            }
            $array["image"] = $imageurls[$i];
            $slide = slide::create_from_array($array);
            $slides[] = $slide;
        }
        return $slides;
    }

    /**
     * Gets the name of a {@see slide} property as used in the block configuration.
     *
     * @param $propertyname the {@see slide}'s name property.
     * @return string The configuration property associated.
     */
    public static function get_config_property_name($propertyname): string {
        return "slide_$propertyname";
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
    public function get_number_of_items() {
        return count($this->config->slide_id);
    }

    /**
     * A method to get all the image urls from their image ids.
     *
     * @param $itemids
     * @return array An array of all the images moodle urls.
     */
    public function get_image_urls(): array {
        $imageurls = [];
        $fs = get_file_storage();
        for ($i = 0; $i < $this->get_number_of_items(); $i++) {
            $allfiles = $fs->get_area_files($this->context->id, 'block_envf_slider', 'images', $i);
            foreach ($allfiles as $file) {
                if ($file->is_valid_image()) {
                    $imageurl = moodle_url::make_pluginfile_url(
                        $this->context->id,
                        'block_envf_slider',
                        'images',
                        $i,
                        $file->get_filepath(),
                        $file->get_filename()
                    )->out(false);
                    $imageurls[] = $imageurl;
                    break;
                }
            }
        }
        return $imageurls;
    }
}
