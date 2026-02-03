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
     * @param object|bool $archiveProps Archive properties from customizer
     */
    public function __construct(
        private string $postType,
        private object|bool $archiveProps
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
     * @return array
     */
    private function buildAttributes(): array
    {
        $attributes = [
            'postType' => $this->postType,
        ];

        // Add archive properties if they exist
        if (is_object($this->archiveProps)) {
            $attributes['archiveProps'] = $this->filterJsonSafeObject($this->archiveProps);
        }

        return $attributes;
    }

    /**
     * Filter an object to only include JSON-serializable properties
     *
     * @param object $object The object to filter
     * @return array The filtered properties as an array
     */
    private function filterJsonSafeObject(object $object): array
    {
        $safe = [];
        foreach (get_object_vars($object) as $key => $value) {
            if (is_scalar($value) || is_null($value)) {
                $safe[$key] = $value;
            } elseif (is_array($value)) {
                $safe[$key] = $this->filterJsonSafeArray($value);
            } elseif (is_object($value)) {
                $safe[$key] = $this->filterJsonSafeObject($value);
            }
        }
        return $safe;
    }

    /**
     * Recursively filter an array to only include JSON-serializable values
     *
     * @param array $array The array to filter
     * @return array The filtered array containing only JSON-safe values
     */
    private function filterJsonSafeArray(array $array): array
    {
        $safe = [];
        foreach ($array as $key => $value) {
            if (is_scalar($value) || is_null($value)) {
                $safe[$key] = $value;
            } elseif (is_array($value)) {
                $safe[$key] = $this->filterJsonSafeArray($value);
            } elseif (is_object($value)) {
                $safe[$key] = $this->filterJsonSafeObject($value);
            }
        }
        return $safe;
    }
}
