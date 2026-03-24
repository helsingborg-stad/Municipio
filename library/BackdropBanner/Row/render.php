<?php

use ComponentLibrary\Renderer\BladeService\BladeServiceFactory;
use ComponentLibrary\Renderer\Renderer as BladeRenderer;
use Municipio\BackdropBanner\Row\Render\BlockRenderer;
use Municipio\Helper\WpService;

$wpService = WpService::get();
$bladeRenderer = new BladeRenderer((new BladeServiceFactory($wpService))->create(BlockRenderer::getViewPathsDir()));

$renderer = new BlockRenderer($wpService, $bladeRenderer);

echo $renderer->render(array_merge($attributes, [
	'content' => $content ?? '',
]));