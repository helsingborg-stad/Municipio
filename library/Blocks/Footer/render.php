<?php

namespace Municipio\Blocks\Header;

use Municipio\Helper\AcfService;
use Municipio\Helper\WpService;

$siteSwitcher = new \Municipio\Helper\SiteSwitcher\SiteSwitcher(WpService::get(), AcfService::get());
$data = new \Municipio\Blocks\Footer\Data(WpService::get(), $siteSwitcher);
$data = $data->getData();

$attributesToCssVars = [
    'textColor' => '--c-footer-color-text',
];

$data['style'] = '';

foreach ($attributesToCssVars as $attribute => $cssVar) {
    if (isset($attributes[$attribute])) {
        $data['style'] .= $cssVar . ': var(--wp--preset--color--' . $attributes[$attribute] . '); ';
    }
}

echo render_blade_view('footer-block', $data, true);
