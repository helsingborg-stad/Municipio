<?php

namespace Municipio\Styleguide\AddLayerOrderDefinitionToHead;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

class AddLayerOrderDefinitionToHead implements Hookable
{
    public function __construct(
        private AddFilter $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio\MarkupProcessor', [$this, 'process']);
    }

    public function process(string $markup): string
    {
        $styleTag = '<style>@layer wordpress, generic, elements, objects, components, icons, utilities, theme;</style>';

        // Add as first child of head
        return preg_replace('/<head(.*?)>/', '<head$1>' . $styleTag, $markup, 1);
    }
}
