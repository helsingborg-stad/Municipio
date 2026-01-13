<?php

use Municipio\Helper\AcfService;
use Municipio\Helper\WpService;
use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolver;

$wpService = WpService::get();
$acfService = AcfService::get();
$wpdb = $GLOBALS['wpdb'];

$renderer = new \Municipio\PostsList\Block\PostsListBlockRenderer\PostsListBlockRenderer(
    new \Municipio\PostsList\PostsListFactory($wpService, $wpdb, new SchemaToPostTypeResolver($acfService, $wpService)),
    new \ComponentLibrary\Renderer\Renderer((new \ComponentLibrary\Renderer\BladeService\BladeServiceFactory($wpService))->create([\Municipio\PostsList\PostsListFeature::getTemplateDir()])),
    $wpService,
);

echo $renderer->render($attributes, $content, $block);
