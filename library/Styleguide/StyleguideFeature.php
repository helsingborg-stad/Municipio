<?php

namespace Municipio\Styleguide;

use Municipio\HooksRegistrar\Hookable;
use WpService\WpService;

class StyleguideFeature implements Hookable
{
    public function __construct(
        private WpService $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', fn() => $this->outputStyleguideCss());
        $this->wpService->addAction('login_enqueue_scripts', fn() => $this->outputStyleguideCss());
        $this->wpService->addAction('enqueue_block_editor_assets', fn() => $this->outputStyleguideCss(true));
        (new ApplyLayerToInlineStyles\ApplyLayerToInlineStyles($this->wpService))->addHooks();
        (new AddLayerOrderDefinitionToHead\AddLayerOrderDefinitionToHead($this->wpService))->addHooks();
        (new ApplyLayersToEnqueuedStyles\ApplyLayersToEnqueuedStyles($this->wpService))->addHooks();
        (new Customize\Customize($this->wpService))->addHooks();
    }

    public function outputStyleguideCss(bool $isEditor = false): void
    {
        if ($isEditor) {
            add_editor_style($this->getStyleguideTemplatePath());
        } else {
            $this->wpService->wpEnqueueStyle('styleguide-css-variables', get_template_directory_uri() . $this->getStyleguideTemplatePath());
        }
    }

    private function getStyleguideTemplatePath(): string
    {
        return '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/styleguide.css');
    }
}
