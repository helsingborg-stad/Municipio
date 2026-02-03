<?php

namespace Municipio\Controller\Archive;

use Municipio\Helper\WpService;

/**
 * Reconstructs full archive data from minimal async attributes.
 *
 * When async requests come in with minimal URL parameters, this class
 * queries WordPress to rebuild the full data array needed by ConfigMapper.
 *
 * Follows Single Responsibility Principle - only responsible for data reconstruction.
 */
class AsyncAttributesReconstructor
{
    /**
     * Reconstruct full archive data from minimal async attributes.
     *
     * Takes minimal identifiers (postType, archivePropsKey) and queries
     * WordPress to rebuild the complete data array.
     *
     * @param array $minimalAttributes Minimal attributes from URL
     * @param WpService|null $wpService Optional WpService instance
     * @return array Full reconstructed data array
     */
    public static function reconstruct(array $minimalAttributes, ?WpService $wpService = null): array
    {
        try {
            if ($wpService === null) {
                $wpService = WpService::get();
            }
        } catch (\Throwable $e) {
            // Fallback if WpService not available
            $wpService = null;
        }

        $postType        = $minimalAttributes['postType'] ?? 'page';
        $queryVarsPrefix = $minimalAttributes['queryVarsPrefix'] ?? 'archive_';
        $archivePropsKey = $minimalAttributes['archivePropsKey'] ?? null;

        // Get customizer data from WordPress (this is cached by WordPress)
        $customizer = new \stdClass();
        if ($wpService && method_exists($wpService, 'getThemeMod')) {
            $customizer = $wpService->getThemeMod('customizer_option') ?? new \stdClass();
        } elseif (function_exists('get_theme_mod')) {
            $customizerData = get_theme_mod('customizer_option');
            if ($customizerData) {
                $customizer = is_object($customizerData) ? $customizerData : (object)$customizerData;
            }
        }

        // Get archive properties if key is provided
        $archiveProps = false;
        if ($archivePropsKey && is_object($customizer) && isset($customizer->{$archivePropsKey})) {
            $archiveProps = is_object($customizer->{$archivePropsKey})
                ? $customizer->{$archivePropsKey}
                : (object) $customizer->{$archivePropsKey};
        }

        // Reconstruct the full data array
        return [
            'postType'           => $postType,
            'queryVarsPrefix'    => $queryVarsPrefix,
            'customizer'         => $customizer,
            'archiveProps'       => $archiveProps,
            'wpTaxonomies'       => $GLOBALS['wp_taxonomies'] ?? [],
            'wpService'          => $wpService,
            'wpdb'               => $GLOBALS['wpdb'] ?? null,
            'displayArchiveLoop' => true,
        ];
    }

    /**
     * Enrich minimal attributes with reconstructed data.
     *
     * This method is useful when you want to keep the original attributes
     * and just add the reconstructed data to them.
     *
     * @param array $attributes Original attributes from async request
     * @param WpService|null $wpService Optional WpService instance
     * @return array Enriched attributes with reconstructed data
     */
    public static function enrich($attributes, ?WpService $wpService = null): array
    {
        try {
            $reconstructed = self::reconstruct($attributes, $wpService);
            return array_merge($reconstructed, $attributes);
        } catch (\Throwable $e) {
            // If reconstruction fails, return original attributes
            // This ensures the system continues to work even if reconstruction fails
            error_log('AsyncAttributesReconstructor::enrich failed: ' . $e->getMessage());
            return $attributes;
        }
    }
}
