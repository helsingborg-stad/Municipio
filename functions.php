<?php

define('MUNICIPIO_PATH', get_template_directory() . '/');

require_once MUNICIPIO_PATH . '/library/Bootstrap.php';

add_action('after_setup_theme', function () {
    load_theme_textdomain('municipio', get_template_directory() . '/languages');
});

add_action('init', function () {
    $term = get_term(49042, 'activity');
    // $term = get_queried_object();
    // echo '<pre>' . print_r($term, true) . '</pre>';
    $termIcon = \Municipio\Helper\Term::getTermIcon($term);
    echo '<pre>' . var_export($termIcon, true) . '</pre>';
    // die;
});
