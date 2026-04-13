<?php

namespace Municipio\Styleguide\AddLayerOrderDefinitionToHead;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddAction;

class AddLayerOrderDefinitionToHead implements Hookable
{
    public function __construct(
        private AddAction $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addAction('template_redirect', [$this, 'process']);
        $this->wpService->addAction('admin_init', [$this, 'process']);
    }

    public function process(): void
    {
        ob_start(function ($html) {
            $styleTag = '<style>@layer wordpress, generic, elements, objects, components, icons, utilities, theme;</style>';
            // Add as first child of head
            return preg_replace('/<head(.*?)>/', '<head$1>' . $styleTag, $html, 1);
        });
    }
}
