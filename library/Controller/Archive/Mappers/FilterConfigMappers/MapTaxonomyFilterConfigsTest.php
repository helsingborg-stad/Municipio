<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\ApplyFilters;
use WpService\Contracts\GetQueriedObject;

class MapTaxonomyFilterConfigsTest extends TestCase
{
    #[TestDox('map() normalizes scalar enabled filters to an array before mapping taxonomies')]
    public function testMapNormalizesScalarEnabledFiltersToArrayBeforeMappingTaxonomies(): void
    {
        $archiveProps = (object) [
            'enabledFilters' => 'category',
            'categoryFilterFieldType' => 'multi',
        ];
        $mapper = new MapTaxonomyFilterConfigs(
            [$this->taxonomy('category')],
            new class implements ApplyFilters, GetQueriedObject {
                public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
                {
                    return $value;
                }

                public function getQueriedObject(): \WP_Term|\WP_Post_Type|\WP_Post|\WP_User|null
                {
                    return null;
                }
            },
        );

        $result = $mapper->map(['archiveProps' => $archiveProps]);

        $this->assertSame(['category'], $archiveProps->enabledFilters);
        $this->assertCount(1, $result);
    }

    /**
     * Create taxonomy object for mapper tests.
     *
     * @param string $name Taxonomy name.
     * @return \WP_Taxonomy
     */
    private function taxonomy(string $name): \WP_Taxonomy
    {
        $taxonomy = new \WP_Taxonomy([], $name);
        $taxonomy->name = $name;

        return $taxonomy;
    }
}
