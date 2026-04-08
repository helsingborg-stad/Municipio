<?php

namespace Municipio\StyleguideCss\ApplyLayerToInlineStyles;

use Municipio\HooksRegistrar\Hookable;
use Municipio\MarkupProcessor\MarkupProcessorInterface;
use WpService\Contracts\AddFilter;

/**
 * This class wraps all inline styles in a @layer to ensure they are applied after the theme's styles.
 *
 * E.g. <style>body { background: red; }</style> becomes <style>@layer wordpress { body { background: red; } }</style>
 */
class ApplyLayerToInlineStyles implements MarkupProcessorInterface, Hookable
{
    private const string LAYER_NAME = 'wordpress';

    public function __construct(
        private AddFilter $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('Municipio\MarkupProcessor', [$this, 'process']);
    }

    public function process(string $markup): string
    {
        // Regex to match <style> tags that do not already contain @layer
        $styleTagPattern = '/<style\b([^>]*)>(?![^<]*@layer)(.*?)<\/style>/is';

        // Callback to wrap inline styles in @layer, unless they already use layer()
        $wrapInLayer = function ($matches) {
            $attributes = $matches[1];
            $cssContent = $matches[2];

            // Skip if the style contains "layer("
            if (stripos($cssContent, 'layer(') !== false) {
                return $matches[0];
            }

            return '<style' . $attributes . '>@layer ' . self::LAYER_NAME . ' {' . $cssContent . '}</style>';
        };

        return preg_replace_callback(
            $styleTagPattern,
            $wrapInLayer,
            $markup,
        );
    }
}
