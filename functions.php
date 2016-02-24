<?php

require_once get_template_directory() . '/library/Bootstrap.php';

add_action('after_setup_theme', function () {
    load_theme_textdomain('municipio', get_template_directory() . '/languages');
});
