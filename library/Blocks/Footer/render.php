<?php

namespace Municipio\Blocks\Header;

use Municipio\Helper\AcfService;
use Municipio\Helper\WpService;

$siteSwitcher = new \Municipio\Helper\SiteSwitcher\SiteSwitcher(WpService::get(), AcfService::get());
$controller = new \Municipio\Blocks\Footer\Data(WpService::get(), $siteSwitcher);

$data = $controller->getData();

echo render_blade_view('partials.footer', $data);
