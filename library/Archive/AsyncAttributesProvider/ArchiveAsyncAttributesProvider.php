<?php

declare(strict_types=1);

namespace Municipio\Archive\AsyncAttributesProvider;

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
     */
    public function __construct(
        private string $postType,
        private object $archiveProps
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
        ];
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
