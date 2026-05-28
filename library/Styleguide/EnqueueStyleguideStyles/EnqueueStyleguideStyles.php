<?php

namespace Municipio\Styleguide\EnqueueStyleguideStyles;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;
use WpService\Contracts\AddEditorStyle;
use WpService\Contracts\GetTemplateDirectoryUri;
use WpService\Contracts\WpEnqueueStyle;

class EnqueueStyleguideStyles implements Hookable
{
    public function __construct(
        private AddAction&WpEnqueueStyle&GetTemplateDirectoryUri&AddEditorStyle $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('wp_enqueue_scripts', fn() => $this->outputStyleguideCss());
        $this->wpService->addAction('login_enqueue_scripts', fn() => $this->outputStyleguideCss());
        $this->wpService->addAction('enqueue_block_editor_assets', fn() => $this->outputStyleguideCss(true));
    }

    public function outputStyleguideCss(bool $isEditor = false): void
    {
        if ($isEditor) {
            $this->wpService->addEditorStyle($this->getStyleguideTemplatePath());
        } else {
            $this->wpService->wpEnqueueStyle('styleguide-css-variables', $this->wpService->getTemplateDirectoryUri() . $this->getStyleguideTemplatePath());
        }
    }

    private function getStyleguideTemplatePath(): string
    {
        return '/assets/dist/' . \Municipio\Helper\CacheBust::name('css/styleguide.css');
    }
}
