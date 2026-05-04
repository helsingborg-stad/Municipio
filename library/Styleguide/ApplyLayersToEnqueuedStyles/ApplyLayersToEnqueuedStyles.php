<?php

namespace Municipio\Styleguide\ApplyLayersToEnqueuedStyles;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Applies a CSS layer to specific WordPress stylesheets by filtering the style loader tag.
 */
class ApplyLayersToEnqueuedStyles implements Hookable
{
    private const LAYER_NAME = 'wordpress';
    public const HANDLES = [
        'wp-block-library' => 'wordpress',
        'common' => 'wordpress',
        'municipio' => 'wordpress',
        'css-municipiocss' => 'theme',
    ];

    public function __construct(
        private AddFilter $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('style_loader_tag', [$this, 'apply'], 10, 2);
    }

    public function apply(string $tag, string $handle): string
    {
        if (!in_array($handle, array_keys(self::HANDLES))) {
            return $tag;
        }

        $url = $this->extractImportUrl($tag);

        if ($url) {
            return "<style>@import url(\"$url\") layer(" . self::HANDLES[$handle] . ');</style>';
        }

        return $tag;
    }

    private function extractImportUrl(string $tag): ?string
    {
        return preg_match('/href=[\'"]([^\'"]+)[\'"]/', $tag, $matches) ? $matches[1] : null;
    }
}
