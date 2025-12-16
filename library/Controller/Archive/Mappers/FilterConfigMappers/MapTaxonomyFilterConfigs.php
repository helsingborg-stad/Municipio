<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use Municipio\Controller\Archive\Mappers\MapperInterface;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterConfig;
use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetQueriedObject;

/**
 * Map taxonomy filter configs
 */
class MapTaxonomyFilterConfigs implements MapperInterface
{
    /**
     * Constructor
     *
     * @param array $wpTaxonomies
     * @param ApplyFilters&GetQueriedObject $wpService
     */
    public function __construct(
        private array $wpTaxonomies,
        private ApplyFilters&GetQueriedObject $wpService,
    ) {}

    /**
     * Map taxonomy filter configs
     *
     * @param array $data
     * @return mixed
     */
    public function map(array $data): mixed
    {
        $taxonomies = $this->wpService->applyFilters(
            'Municipio/Archive/getTaxonomyFilters/taxonomies',
            array_diff(
                $data['archiveProps']->enabledFilters ?? [],
                [$this->currentTaxonomy()],
            ),
            $this->currentTaxonomy(),
        );

        if (empty($taxonomies)) {
            return [];
        }

        // Wash out invalid taxonomies
        $filteredWpTaxonomies = array_filter(
            $this->wpTaxonomies,
            fn(\WP_Taxonomy $wpTaxonomy) => in_array($wpTaxonomy->name, $taxonomies),
        );

        // $taxonomies    = array_values(array_intersect($allTaxonomies, $taxonomies));
        return array_map(function (\WP_Taxonomy $taxonomy) use ($data) {
            $camelCasedName = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $taxonomy->name))));
            $filterType = isset($data['archiveProps']->{$camelCasedName . 'FilterFieldType'}) && $data['archiveProps']->{$camelCasedName . 'FilterFieldType'} === 'multi' ? TaxonomyFilterType::MULTISELECT : TaxonomyFilterType::SINGLESELECT;
            return new TaxonomyFilterConfig($taxonomy, $filterType);
        }, $filteredWpTaxonomies);
    }

    /**
     * Get the current taxonomy page
     */
    private function currentTaxonomy()
    {
        $queriedObject = $this->wpService->getQueriedObject();
        $isTaxArchive = false;
        if (!empty($queriedObject->taxonomy) && isset($_SERVER['REQUEST_URI'])) {
            $pathParts = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
            $trimmedPath = end($pathParts);
            if ($queriedObject->slug == $trimmedPath) {
                $isTaxArchive = $queriedObject->taxonomy;
            }
        }
        return $isTaxArchive;
    }
}
