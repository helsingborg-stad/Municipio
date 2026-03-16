<?php

namespace Municipio\Blocks\Footer;

use Municipio\Helper\AcfService;
use Municipio\Helper\WpService;

$siteSwitcher = new \Municipio\Helper\SiteSwitcher\SiteSwitcher(WpService::get(), AcfService::get());
$data = new \Municipio\Blocks\Footer\Data(WpService::get(), $siteSwitcher);
$data = $data->getData();

echo render_blade_view('footer-block', $data, true);
