<?php

namespace Municipio\Controller\Archive;

/**
 * Extracts and filters source attributes for async requests.
 *
 * Preserves all necessary archive configuration data while filtering out
 * non-serializable data like closures and large objects.
 */
class SourceAttributesExtractor implements AsyncConfigExtractorInterface
{
    private array $sourceData;

    /**
     * Keys to include in source attributes (minimal set for async reconstruction).
     *
     * Using an allowlist approach instead of blocklist to ensure minimal URL data.
     * Only include essential identifiers - backend will query/reconstruct everything else.
     */
    private const ALLOWED_KEYS = [
        'postType',
        'queryVarsPrefix',
        'archivePropsKey',
    ];

    /**
     * Additional keys to exclude from source attributes (non-serializable or unnecessary for async).
     * Only used as a fallback if ALLOWED_KEYS approach is not suitable.
     */
    private const EXCLUDED_KEYS = [
        'wpQuery',
        'posts',
        'wpHeader',
        'wpFooter',
        'wpService',
        'wpdb',
        'acfService',
        'wpTaxonomies',
        'customizer',
        'archiveProps',
        'archiveTitle',
        'archiveLead',
        'archiveMenuItems',
        // Exclude callable functions
        'getTags',
        'getExcerpt',
        'getReadingTime',
        'showDateBadge',
        'getParentColumnClasses',
        'getPostColumnClasses',
        'getDateTimestamp',
        'getDateFormat',
        'shouldDisplayPlaceholderImage',
        'getTableComponentArguments',
        'getSchemaProjectProgressLabel',
        'getSchemaProjectProgressPercentage',
        'getSchemaProjectTechnologyTerms',
        'getSchemaProjectCategoryTerms',
        'getSchemaEventHasMoreOccasions',
        'getEventMoreOccasionsLabel',
        'getSchemaEventPlaceName',
        'getSchemaEventDate',
        'getSchemaEventDateBadgeDate',
        'getSchemaEventPermalink',
        'getSchemaExhibitionOccasionText',
        'getTaxonomyFilterSelectComponentArguments',
        'getFilterFormSubmitButtonArguments',
        'getFilterFormResetButtonArguments',
        'getTextSearchFieldArguments',
        'getDateFilterFieldArguments',
        'getPaginationComponentArguments',
        'getAsyncAttributes',
        // Exclude config objects (they'll be recreated from source data)
        'appearanceConfig',
        'filterConfig',
    ];

    public function __construct(array $sourceData)
    {
        $this->sourceData = $sourceData;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(): array
    {
        return [
            'sourceAttributes' => $this->filterSourceData($this->sourceData),
        ];
    }

    /**
     * Filter source data to include only minimal identifiers.
     *
     * Uses allowlist approach to ensure minimal URL data.
     * Only includes essential identifiers - backend will reconstruct everything else.
     *
     * @param array $data The source data
     * @return array Minimal filtered data safe for JSON serialization
     */
    private function filterSourceData(array $data): array
    {
        $filtered = [];

        foreach ($data as $key => $value) {
            // Allowlist approach: only include explicitly allowed keys
            if (!in_array($key, self::ALLOWED_KEYS, true)) {
                continue;
            }

            // Skip closures and non-serializable objects
            if ($value instanceof \Closure) {
                continue;
            }

            if (is_object($value) && !($value instanceof \stdClass)) {
                // Try to convert to array if possible
                if (method_exists($value, 'toArray')) {
                    $filtered[$key] = $value->toArray();
                } elseif ($value instanceof \JsonSerializable) {
                    $filtered[$key] = $value;
                } else {
                    // Skip complex objects that can't be serialized
                    continue;
                }
            } elseif (is_array($value)) {
                // For arrays, just include primitive values (don't go deep)
                $filtered[$key] = $this->filterArrayShallow($value);
            } else {
                // Include primitive values
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Shallow filter for arrays - only includes primitive values.
     *
     * Used by the allowlist approach to keep data minimal.
     *
     * @param array $array The array to filter
     * @return array Filtered array with only primitive values
     */
    private function filterArrayShallow(array $array): array
    {
        $filtered = [];

        foreach ($array as $key => $value) {
            // Only include primitive values, skip objects, closures, and nested arrays
            if (is_scalar($value) || is_null($value)) {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }

    /**
     * Recursively filter an array to remove non-serializable items.
     *
     * @param array $array The array to filter
     * @return array Filtered array
     */
    private function filterArray(array $array): array
    {
        $filtered = [];

        foreach ($array as $key => $value) {
            if ($value instanceof \Closure) {
                continue;
            }

            if (is_object($value) && !($value instanceof \stdClass) && !($value instanceof \JsonSerializable)) {
                continue;
            }

            if (is_array($value)) {
                $filtered[$key] = $this->filterArray($value);
            } else {
                $filtered[$key] = $value;
            }
        }

        return $filtered;
    }
}
