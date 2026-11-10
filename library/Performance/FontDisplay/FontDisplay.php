<?php

declare(strict_types=1);

namespace Municipio\Performance\FontDisplay;

use Municipio\HooksRegistrar\Hookable;
use WpService\Contracts\AddFilter;

/**
 * Makes WordPress Font Library faces render text immediately.
 *
 * WordPress uses `fallback` as the default font-display value for Font Library
 * faces. Applying `swap` at the user Global Styles layer covers both existing
 * and newly activated fonts without modifying a site's stored font settings.
 */
class FontDisplay implements Hookable
{
    public function __construct(private readonly AddFilter $wpService)
    {
    }

    public function addHooks(): void
    {
        $this->wpService->addFilter('wp_theme_json_data_user', [$this, 'useSwapForFontLibraryFaces']);
    }

    /**
     * Replaces the Font Library default with swap, while retaining deliberate
     * non-default strategies such as optional.
     *
     * @param mixed $themeJson WP_Theme_JSON_Data when Global Styles is available.
     * @return mixed
     */
    public function useSwapForFontLibraryFaces(mixed $themeJson): mixed
    {
        if (!is_object($themeJson) || !method_exists($themeJson, 'get_data') || !method_exists($themeJson, 'update_with')) {
            return $themeJson;
        }

        $data = $themeJson->get_data();

        if (!is_array($data)) {
            return $themeJson;
        }

        $fontFamilies = $data['settings']['typography']['fontFamilies'] ?? [];

        if (!is_array($fontFamilies)) {
            return $themeJson;
        }

        $hasChanges = false;

        foreach ($fontFamilies as $origin => $families) {
            foreach ((array) $families as $familyIndex => $family) {
                foreach ((array) ($family['fontFace'] ?? []) as $faceIndex => $face) {
                    if (($face['fontDisplay'] ?? 'fallback') !== 'fallback') {
                        continue;
                    }

                    $data['settings']['typography']['fontFamilies'][$origin][$familyIndex]['fontFace'][$faceIndex]['fontDisplay'] = 'swap';
                    $hasChanges = true;
                }
            }
        }

        return $hasChanges ? $themeJson->update_with($data) : $themeJson;
    }
}
