<?php

namespace Municipio\Blocks\Header;

use Municipio\Helper\AcfService;
use Municipio\Helper\User\User;
use Municipio\Helper\WpService;

$menuConfig = new \Municipio\Controller\Navigation\Config\MenuConfig();
$menuBuilder = new \Municipio\Controller\Navigation\MenuBuilder($menuConfig, AcfService::get(), WpService::get());
$menuDirector = new \Municipio\Controller\Navigation\MenuDirector($menuBuilder);
$siteSwitcher = new \Municipio\Helper\SiteSwitcher\SiteSwitcher(WpService::get(), AcfService::get());
$controller = new \Municipio\Blocks\Header\Data($menuBuilder, $menuDirector, WpService::get(), $siteSwitcher, User::get());

$data = $controller->getData();

echo render_blade_view('blocks.header', $data);
