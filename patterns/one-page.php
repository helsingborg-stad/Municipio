<?php

/**
 * Title: One Page
 * Slug: municipio/one-page
 * Categories: featured
 */

use Municipio\Helper\AcfService;
use Municipio\Helper\User\User;
use Municipio\Helper\WpService;

$menuConfig = new \Municipio\Controller\Navigation\Config\MenuConfig();
$menuBuilder = new \Municipio\Controller\Navigation\MenuBuilder($menuConfig, AcfService::get(), WpService::get());
$menuDirector = new \Municipio\Controller\Navigation\MenuDirector($menuBuilder);
$siteSwitcher = new \Municipio\Helper\SiteSwitcher\SiteSwitcher(WpService::get(), AcfService::get());
$controller = new \Municipio\Controller\BaseController($menuBuilder, $menuDirector, WpService::get(), AcfService::get(), $siteSwitcher, User::get());

$data = $controller->getData();

echo render_blade_view('one-page', $data);
