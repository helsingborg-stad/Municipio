<?php

declare(strict_types=1);

namespace Municipio\Styleguide\ApplyLayersToEnqueuedStyles;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;
use WpService\Contracts\GetCurrentScreen;
use WpService\Contracts\IsAdmin;

/**
 * Applies a CSS layer to specific WordPress stylesheets by filtering the style loader tag.
 */
class ApplyLayersToEnqueuedStyles implements Hookable
{
    public const HANDLES = [
        'municipio' => 'wordpress',
        'css-municipiocss' => 'theme',
        'admin-bar' => 'theme',
    ];

    public const patterns = [
        'wp-includes' => 'wordpress',
        'wp-admin' => 'wordpress',
    ];

    public function __construct(
        private AddFilter&IsAdmin&GetCurrentScreen $wpService,
    ) {}

    public function addHooks(): void
    {
        $this->wpService->addFilter('style_loader_tag', [$this, 'apply'], 10, 3);
    }

    public function apply(string $tag, string $handle, string $href): string
    {
        // Load only in frontend and in gutenberg editor, not in wp-admin to avoid breaking admin styles.
        if ($this->shouldApplyLayer() === false) {
            return $tag;
        }

        if ($layer = $this->getLayerByHandle($handle)) {
            return $this->buildLayeredStyleTag($tag, $href, $layer);
        }

        if ($layer = $this->getLayerByPattern($href)) {
            return $this->buildLayeredStyleTag($tag, $href, $layer);
        }

        return $tag;
    }

    private function buildLayeredStyleTag(string $tag, string $href, string $layer): string
    {
        $idAttribute = $this->getIdAttribute($tag);

        return "<style{$idAttribute}>@import url(\"{$href}\") layer({$layer});</style>";
    }

    private function getIdAttribute(string $tag): string
    {
        if (!preg_match('/\sid=["\']([^"\']+)["\']/i', $tag, $matches)) {
            return '';
        }

        return ' id="' . htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8') . '"';
    }

    private function shouldApplyLayer(): bool
    {
        if (!$this->wpService->isAdmin()) {
            return true;
        }

        if ($this->wpService->getCurrentScreen()?->is_block_editor()) {
            return true;
        }

        return false;
    }

    private function getLayerByHandle(string $handle): ?string
    {
        return self::HANDLES[$handle] ?? null;
    }

    private function getLayerByPattern(string $href): ?string
    {
        foreach (self::patterns as $pattern => $layer) {
            if (str_contains($href, $pattern)) {
                return $layer;
            }
        }

        return null;
    }
}
