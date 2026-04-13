<?php

namespace Municipio\Styleguide\ApplyLayerToWordpressStyles;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Applies a CSS layer to specific WordPress stylesheets by filtering the style loader tag.
 */
class ApplyLayerToWordpressStyles implements Hookable
{
    private const string LAYER_NAME = 'wordpress';
    private const array HANDLES = [
        'wp-block-library',
        'common',
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
        if (!in_array($handle, self::HANDLES)) {
            return $tag;
        }

        $url = $this->extractImportUrl($tag);

        if ($url) {
            return "<style>@import url(\"$url\") layer(" . self::LAYER_NAME . ');</style>';
        }

        return $tag;
    }

    private function extractImportUrl(string $tag): ?string
    {
        return preg_match('/href=[\'"]([^\'"]+)[\'"]/', $tag, $matches) ? $matches[1] : null;
    }
}
