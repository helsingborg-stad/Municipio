<?php

define('MUNICIPIO_PATH', get_template_directory() . '/');

require_once MUNICIPIO_PATH . '/library/Bootstrap.php';

add_filter("use_block_editor_for_post_type", "activate_gutenberg_editor");

function activate_gutenberg_editor() {
    return get_field('activate_gutenberg_editor', 'option');
}


add_action('after_setup_theme', function () {
    load_theme_textdomain('municipio', get_template_directory() . '/languages');
});
