<?php

define('MUNICIPIO_PATH', get_template_directory() . '/');

require_once MUNICIPIO_PATH . '/library/Bootstrap.php';

add_action('after_setup_theme', function () {
    load_theme_textdomain('municipio', get_template_directory() . '/languages');
});