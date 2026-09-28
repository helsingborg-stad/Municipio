<?php

use Municipio\Helper\AcfService;
use Municipio\Helper\Renderer\ClosureSafeRenderer;
use Municipio\Helper\WpService;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolver;

$wpService = WpService::get();
$acfService = AcfService::get();
$wpdb = $GLOBALS['wpdb'];

$renderer = new \Municipio\PostsList\Block\PostsListBlockRenderer\PostsListBlockRenderer(
    new \Municipio\PostsList\PostsListFactory($wpService, $wpdb, new SchemaToPostTypeResolver($acfService, $wpService)),
    new ClosureSafeRenderer(\Municipio\Helper\ComponentBladeService::create([\Municipio\PostsList\PostsListFeature::getTemplateDir()], $wpService)),
    $wpService,
);

echo $renderer->render($attributes, $content, $block);
