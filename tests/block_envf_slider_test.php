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
    private $block;

    /**
     * A method to initialize a block to be able to test properly all the {@see block_envf_slider}'s methods.
     *
     * @return void
     */
    public function setUp(): void {
        global $CFG;
        $this->resetAfterTest();
        $this->user = $this->getDataGenerator()->create_user();
        $this->setUser($this->user);
        // Create a ENVF Slider block.
        $page = $this->create_dummy_page();
        $blockname = 'envf_slider';
        $page->blocks->add_block_at_end_of_default_region($blockname);
        // Here we need to work around the block API. In order to get 'get_blocks_for_region' to work,
        // we would need to reload the blocks (as it has been added to the DB but is not
        // taken into account in the block manager).
        // The only way to do it is to recreate a page so it will reload all the block.
        // It is a main flaw in the  API (not being able to use load_blocks twice).
        // Alternatively if birecordsbyregion was nullable,
        // should for example have a load_block + create_all_block_instances and
        // should be able to access to the block.
        $page = $this->create_dummy_page();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        $block = block_instance($blockname, $block->instance);
        $this->block = $block;
        $this->upload_files_in_block([
            $CFG->dirroot . "/blocks/envf_slider/tests/fixtures/pexels-tom-dubois-17088081.jpg",
            $CFG->dirroot . "/blocks/envf_slider/tests/fixtures/openclipart-342997.png"
        ]);
        // Reload config.
        $page = $this->create_dummy_page();
        $blocks = $page->blocks->get_blocks_for_region($page->blocks->get_default_region());
        $block = end($blocks);
        $this->block = block_instance($blockname, $block->instance); // Refresh the block.
    }

    /**
     * Create a dummy frontpage
     *
     * @return moodle_page
     * @throws \dml_exception
     */
    private function create_dummy_page() {
        $page = new moodle_page();
        $page->set_context(context_system::instance());
        $page->set_pagelayout('frontpage');
        $page->blocks->load_blocks();
        return $page;
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
            $configdata->slide_title[] = 'Title ' . $index;
            $configdata->slide_description[] = 'Description' . $index;
            $configdata->slide_image[] = $file->get_itemid();
        }
        $configdata->slide_count = count($imagesnames);
        $this->block->instance_config_save($configdata);
    }

    /**
     * Tests if the {@see block_envf_slider::get_slide_at()} method returns valid  and useable image urls.
     *
     * @return void
     * @covers \block_envf_slider::get_slide_at
     */
    public function test_get_slide_at() {
        // We do not want to keep the get_image_url public, so we tweak the class for testing.
        $blockref = new \ReflectionClass($this->block);
        $getslidemethod = $blockref->getMethod('get_slide_at');
        $getslidemethod->setAccessible(true);
        $slide = $getslidemethod->invoke($this->block, 0);
        $this->assertNotEmpty($slide);
        $slidedata = $slide->export_for_template($this->block->page->get_renderer('core'));
        $this->assertEquals('pexels-tom-dubois-17088081.jpg', basename($slidedata['image']));
    }

    /**
     * Tests if the {@see block_envf_slider::config_is_valid()} method returns well true if the block's configuration is valid,
     * and false if the block's configuration is not.
     *
     * @param \stdClass $config a block's configuration.
     * @param bool $expectedresult whether the given block configuration is valid or not, to check if the method
     * {@see block_envf_slider::config_is_valid()} returns the right value.
     * @covers       \block_envf_slider::config_is_valid
     * @dataProvider config_provider
     * @return void
     */
    public function test_config_is_valid(\stdClass $config, bool $expectedresult) {
        $this->block->config = $config;
        self::assertEquals($expectedresult, $this->block->config_is_valid());
    }

    /**
     * Tests the preg match expression to retrieve all the block configuration fields that relates to slides.
     *
     * @param string $string a string to test the pregmatch expression.
     * @param bool $isrecognized wether or not the string should be recognized by the preg_match expression.
     * @covers       \block_envf_slider\block_envf_slider_edit_form::delete_slide()
     * @dataProvider preg_match_provider
     * @return void
     */
    public function test_pregmatch_for_config_fields_in_editform(string $string, bool $isrecognized) {
        $pregexpression = '/^slide_\S+/';
        self::assertEquals($isrecognized, (bool) preg_match($pregexpression, $string));
    }

    /**
     * A data provider used in {@see block_envf_slider_test::test_config_is_valid()} to provide some test cases.
     *
     * Note that once we get to php 8.x, we'll be able to check property's types and add these cases :
     *
     *      "Wrong types (str)" => [
     *          (object)[
     *              "slide_title" => ["title"],
     *              "slide_description" => ["description"],
     *              "slide_image" => ["4510"],
     *              "slide_whitetext" => ["true"]
     *          ], false
     *      ],
     *      "Wrong types (int)" => [
     *          (object)[
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
                (object) [
                    "slide_title" => ["title"],
                    "slide_description" => ["description"],
                    "slide_image" => ["4510"],
                    "slide_whitetext" => [true]
                ], true
            ],
        ];
    }

    /**
     * Provider used to test the <pre>preg_match</pre> expression in {@see self::test_pregmatch_for_config_fields_in_editform()}.
     *
     * @return array[]
     */
    public function preg_match_provider() {
        return [
            "valid1" => ["slide_something", true],
            "valid2" => ["slide_other", true],
            "slide plural" => ["slides_something", false],
            "slide_ with nothing after" => ["slide_", false],
            "slide_ in the middle" => ["fjoisdf_slide_something", false],
            "slide in the end" => ["something_slide_", false],
            "slide with nothing after" => ["slide", false],
            "slide incomplete" => ["sli", false]
        ];
    }
}
