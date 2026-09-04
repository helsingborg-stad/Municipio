<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Config;

use AcfService\Contracts\GetField;

/**
 * Reads the configured search index facets from ACF options.
 */
class FacetsConfig
{
    public function __construct(private GetField $acfService) {}

    /**
     * Get configured facets, optionally excluding disabled rows.
     *
     * @return array<int, array{attribute: string, label: string, enabled: bool}>
     */
    public function getFacets(bool $enabledOnly): array
    {
        $facets = $this->acfService->getField('search_index_facets', 'option');

        if (!is_array($facets)) {
            return [];
        }

        return array_values(array_filter(array_map(static function (mixed $facet): ?array {
            if (!is_array($facet) || !is_string($facet['attribute'] ?? null) || $facet['attribute'] === '') {
                return null;
            }

            return [
                'attribute' => $facet['attribute'],
                'label' => is_string($facet['label'] ?? null) ? $facet['label'] : '',
                'enabled' => (bool) ($facet['enabled'] ?? false),
            ];
        }, $facets), static fn(?array $facet): bool => $facet !== null && (!$enabledOnly || $facet['enabled'])));
    }
}