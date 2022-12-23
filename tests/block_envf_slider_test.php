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
 * Base class for unit tests for block_envf_slider.
 *
 * @package   block_envf_slider
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_envf_slider;

use advanced_testcase;
use block_envf_slider;
use context_system;
use context_user;
use core_files_external;
use moodle_page;

/**
 * Unit tests for the block_envf_slider class.
 *
 * @copyright 2022 - CALL Learning - Martin CORNU-MANSUY <martin@call-learning>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_envf_slider_test extends advanced_testcase {

    /**
     * @var block_envf_slider $block an object from the tested class to be used in the tests.
     * It is initialized in the {@see block_envf_slider_test::init()} method.
     */
    private block_envf_slider $block;

    /**
     * A method to initialize a block to be able to test properly all the {@see block_envf_slider}'s methods.
     *
     * @return void
     */
    public function init(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->user = $this->getDataGenerator()->create_user();
        $this->setUser($this->user);
        // Create a Sponsor block.
        $page = new moodle_page();
        $page->set_context(context_system::instance());
        $page->set_pagelayout('frontpage');
        $blockname = 'envf_slider';
        $page->blocks->load_blocks();
        $page->blocks->add_block_at_end_of_default_region($blockname);
        // Here we need to work around the block API. In order to get 'get_blocks_for_region' to work,
        // we would need to reload the blocks (as it has been added to the DB but is not
        // taken into account in the block manager).
        // The only way to do it is to recreate a page so it will reload all the block.
        // It is a main flaw in the  API (not being able to use load_blocks twice).
        // Alternatively if birecordsbyregion was nullable,
        // should for example have a load_block + create_all_block_instances and
        // should be able to access to the block.
        $page = new moodle_page();
        $page->set_context(context_system::instance());
        $page->set_pagelayout('frontpage');
        $page->blocks->load_blocks();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        $block = block_instance($blockname, $block->instance);
        $this->block = $block;
        $this->upload_files_in_block([
            $CFG->dirroot . "/blocks/envf_slider/tests/fixtures/stonks.jpg",
            $CFG->dirroot . "/blocks/envf_slider/tests/fixtures/phpstormlogo.png"
        ]);
    }

    /**
     * Tests if the {@see block_envf_slider::get_image_urls()} method returns valid  and useable image urls.
     *
     * @return void
     * @covers \block_envf_slider::get_image_urls
     */
    public function test_get_image_urls() {
        $this->init();
        $urls = $this->block->get_image_urls();
        foreach ($urls as $url) {
            $parts = parse_url($url);
            $imagepath = $parts['path'];
            // Todo : Check if the imagepath is valid.
        }
    }

    /**
     * Tests if the {@see block_envf_slider::config_is_valid()} method returns well true if the block's configuration is valid,
     * and false if the block's configuration is not.
     *
     * @return void
     * @covers \block_envf_slider::config_is_valid
     * @dataProvider config_provider
     */
    public function test_config_is_valid($config, $expectedresult) {
        self::assertEquals($expectedresult, block_envf_slider::config_is_valid($config));
    }

    /**
     * @return void
     * @covers \block_envf_slider\block_envf_slider_edit_form::delete_slide()
     * @dataProvider preg_match_provider
     */
    public function test_pregmatch_for_config_fields_in_editform($string, $expectedoutput) {
        $pregexpression = '/^slide_\S+/';
        self::assertEquals($expectedoutput, (bool) preg_match($pregexpression, $string));
    }

    /**
     * TODO check this method to initialise block_envf_slider
     *
     * (source : {@see \block_thumblinks_action\block_thumblinks_action_test::upload_files_in_block()})
     *
     * Upload a file/image in the block
     *
     * @param array $imagesnames
     */
    protected function upload_files_in_block($imagesnames) {
        $usercontext = context_user::instance($this->user->id);
        $configdata = (object) [
            'slide_id' => [],
            'slide_title' => [],
            'slide_description' => [],
        ];
        $configdata->slide_image = [];
        foreach ($imagesnames as $index => $filepath) {
            $draftitemid = file_get_unused_draft_itemid();
            $patharray = explode("/", $filepath);
            $filename = end($patharray);
            $filerecord = array(
                'contextid' => $usercontext->id,
                'component' => 'user',
                'filearea' => 'draft',
                'itemid' => $draftitemid,
                'filepath' => "/",
                'filename' => $filename,
            );
            // Create an area to upload the file.
            $fs = get_file_storage();
            // Create a file from the string that we made earlier.
            $file = $fs->create_file_from_pathname(
                $filerecord,
                $filepath
            );
            $configdata->slide_id[] = $index;
            $configdata->slide_title[] = 'Title ' . $index;
            $configdata->slide_description[] = 'Description' . $index;
            $configdata->slide_image[] = $file->get_itemid();
        }
        $this->block->instance_config_save($configdata);
    }

    /**
     * A data provider used in {@see block_envf_slider_test::test_config_is_valid()} to provide some test cases.
     *
     * Note that once we get to php 8.x, we'll be able to check property's types and add these cases :
     *
     *      "Wrong types (str)" => [
     *          (object)[
     *              "slide_id" => ["im an id"],
     *              "slide_title" => ["title"],
     *              "slide_description" => ["description"],
     *              "slide_image" => ["4510"],
     *              "slide_whitetext" => ["true"]
     *          ], false
     *      ],
     *      "Wrong types (int)" => [
     *          (object)[
     *              "slide_id" => [0],
     *              "slide_title" => [0],
     *              "slide_description" => [0],
     *              "slide_image" => [0],
     *              "slide_whitetext" => [0]
     *          ], false
     *      ]
     *
     * @return array[] Some test cases for the {@see block_envf_slider_test::test_config_is_valid()} method.
     */
    public function config_provider(): array {
        return [
            "Valid configuration" => [
                (object)[
                "slide_id" => [0],
                "slide_title" => ["title"],
                "slide_description" => ["description"],
                "slide_image" => ["4510"],
                "slide_whitetext" => [true]
                ], true
            ],
            "Missing a field" => [
                (object)[
                    "slide_title" => ["title"],
                    "slide_description" => ["description"],
                    "slide_image" => ["4510"],
                    "slide_whitetext" => [true]
                ], false
            ],
            "Not as many items for each element" => [
                (object)[
                    "slide_id" => [0],
                    "slide_title" => ["title1", "title2"],
                    "slide_description" => ["description"],
                    "slide_image" => ["4510"],
                    "slide_whitetext" => [true]
                ], false
            ],
        ];
    }

    public function preg_match_provider() {
        return [
            "valid1" => [ "slide_something", true ],
            "valid2" => [ "slide_other", true ],
            "slide plural" => [ "slides_something", false ],
            "slide_ with nothing after" => [ "slide_", false ],
            "slide_ in the middle" => [ "fjoisdf_slide_something", false ],
            "slide in the end" => [ "something_slide_", false ],
            "slide with nothing after" => [ "slide", false ],
            "slide incomplete" => [ "sli", false ]
        ];
    }
}
