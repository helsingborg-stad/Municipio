<?php

/**
 * Title: Header
 * Slug: municipio/header
 * Categories: featured
 */

use Municipio\Controller\Header\Flexible;
use Municipio\Helper\WpService;

// ob_start();
// wp_head();
// $header = apply_filters('Municipio/HeaderHTML', ob_get_clean());
// $header = ob_get_clean();

$themeMods = WpService::get()->getThemeMods();
$themeMods = array_combine(
    array_map(function ($key) {
        return \Municipio\Helper\FormatObject::camelCase($key);
    }, array_keys($themeMods)),
    $themeMods,
);

$flexibleHeader = new Flexible((object) $themeMods);
$data = [
    'headerData' => $flexibleHeader->getHeaderData(),
    'customizer' => (object) [...$themeMods, 'megaMenuMobile' => false],
    'homeUrl' => home_url(),
    'isAuthenticated' => false,
    'headerBrandEnabled' => true,
    'megaMenuMobile' => null,
    'logotype' => 'https://placehold.it/200x50?text=Logo',
    'lang' => (object) [
        'search' => __('Search', 'municipio'),
        'searchQuestion' => __('What are you looking for?', 'municipio'),
        'goToHomepage' => __('Go to homepage', 'municipio'),
        'close' => __('Close', 'municipio'),
    ],
];
echo render_blade_view('partials.header.flexible', $data);
