<?php

use Municipio\Helper\ComponentBladeService;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\BackdropBanner\Row\Render\BlockRenderer;
use Municipio\Helper\WpService;

$wpService = WpService::get();
$bladeRenderer = new BladeRenderer(ComponentBladeService::create(BlockRenderer::getViewPathsDir(), $wpService));

$renderer = new BlockRenderer($wpService, $bladeRenderer);

echo $renderer->render(array_merge($attributes, [
	'content' => $content ?? '',
]));
