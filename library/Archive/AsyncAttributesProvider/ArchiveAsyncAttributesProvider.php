<?php

declare(strict_types=1);

namespace Municipio\Archive\AsyncAttributesProvider;

use WpService\Contracts\GetThemeMod;

/**
 * Archive async attributes provider
 *
 * Provides JSON-serializable attributes for archive pages that can be used
 * for async rendering and client-side hydration. This implementation filters
 * archive properties to ensure only safe, serializable values are included.
 */
class ArchiveAsyncAttributesProvider implements AsyncAttributesProviderInterface
{
    private array $attributes = [];

    /**
     * Constructor
     *
     * @param string $postType The post type of the archive
     * @param object $archiveProps Archive properties from customizer
     * @param GetThemeMod $wpService WordPress service for resolving theme mods
     */
    public function __construct(
        private string $postType,
        private object $archiveProps,
        private GetThemeMod $wpService
    ) {
        $this->attributes = $this->buildAttributes();
    }

    /**
     * Get async attributes
     *
     * @return array Array of JSON-serializable attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Build the attributes array from archive data
     *
     * Returns minimal block-compatible attributes for async rendering.
     * Maps archive customizer properties to block attribute format.
     *
     * @return array
     */
    private function buildAttributes(): array
    {
        $enabledFilters = $this->archiveProps->enabledFilters ?? [];

        return [
            'postType' => $this->postType,
            'dateSource' => $this->archiveProps->dateField ?? 'post_date',
            'dateFormat' => $this->archiveProps->date_format ?? 'date',
            'design' => $this->mapDesign($this->archiveProps->style ?? 'cards'),
            'numberOfColumns' => $this->archiveProps->numberOfColumns ?? 3,
            'postPropertiesToDisplay' => $this->archiveProps->postPropertiesToDisplay ?? [],
            'taxonomiesToDisplay' => $this->archiveProps->taxonomiesToDisplay ?? [],
            'displayFeaturedImage' => $this->archiveProps->featured_image ?? false,
            'displayReadingTime' => $this->archiveProps->reading_time ?? false,
            // Filter settings
            'textSearchEnabled' => in_array('text_search', $enabledFilters),
            'dateFilterEnabled' => in_array('date_range', $enabledFilters),
            // Pagination and ordering
            'postsPerPage' => $this->getPostsPerPage(),
            'orderBy' => $this->archiveProps->orderBy ?? 'post_date',
            'order' => $this->mapOrder($this->archiveProps->orderDirection ?? 'desc'),
        ];
    }

    /**
     * Get posts per page from theme mod
     *
     * @return int
     */
    private function getPostsPerPage(): int
    {
        $postTypeSpecific = $this->wpService->getThemeMod('archive_' . $this->postType . '_post_count', null);
        if ($postTypeSpecific !== null) {
            return (int) $postTypeSpecific;
        }

        return (int) $this->wpService->getThemeMod('archive_post_post_count', 10);
    }

    /**
     * Map order direction to lowercase format
     *
     * @param string $orderDirection Order direction from customizer
     * @return string Lowercase order direction
     */
    private function mapOrder(string $orderDirection): string
    {
        return strtolower($orderDirection) === 'asc' ? 'asc' : 'desc';
    }

    /**
     * Map archive style to block design format
     *
     * @param string $style Archive style from customizer
     * @return string Block design value
     */
    private function mapDesign(string $style): string
    {
        return match ($style) {
            'cards' => 'card',
            'collection' => 'collection',
            'compressed' => 'compressed',
            'grid' => 'block',
            'list' => 'table',
            'newsitem' => 'newsitem',
            'schema' => 'schema',
            default => 'card',
        };
    }
}
