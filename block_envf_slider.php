<?php

class block_envf_slider extends block_base{

    public function __construct() {
        parent::__construct();
    }

    public function init() {

    }

    public function get_content() {
        $this->page->requires->css(
        new moodle_url('/blocks/envf_slider/js/glide/dist/css/glide.core' .
        (debugging() ? '.min' : '') . '.css'));
        return null;
    }

}
