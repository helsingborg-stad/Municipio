<?php

namespace Municipio\Controller\Archive\Mappers\FilterConfigMappers;

use Municipio\PostsList\Config\FilterConfig\TaxonomyFilterConfig\TaxonomyFilterType;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $mapper = new MapTaxonomyFilterConfigs([$this->taxonomy('category')], $this->wpService());

        $result = $mapper->map(['archiveProps' => $archiveProps]);

        $this->assertSame(['category'], $archiveProps->enabledFilters);
        $this->assertCount(1, $result);
    }

    #[TestDox('map() uses the filter field type from archive props, defaulting to single select')]
    #[DataProvider('filterFieldTypeProvider')]
    public function testMapUsesFilterFieldTypeFromArchiveProps(?string $fieldType, TaxonomyFilterType $expected): void
    {
        $archiveProps = (object) ['enabledFilters' => ['category']];
        if ($fieldType !== null) {
            $archiveProps->categoryFilterFieldType = $fieldType;
        }
        $mapper = new MapTaxonomyFilterConfigs([$this->taxonomy('category')], $this->wpService());

        $result = array_values($mapper->map(['archiveProps' => $archiveProps]));

        $this->assertSame($expected, $result[0]->getFilterType());
    }

    public static function filterFieldTypeProvider(): array
    {
        return [
            'multi' => ['multi', TaxonomyFilterType::MULTISELECT],
            'single' => ['single', TaxonomyFilterType::SINGLESELECT],
            'checkbox' => ['checkbox', TaxonomyFilterType::CHECKBOX],
            'unknown' => ['unknown', TaxonomyFilterType::SINGLESELECT],
            'not set' => [null, TaxonomyFilterType::SINGLESELECT],
        ];
    }

    private function wpService(): ApplyFilters&GetQueriedObject
    {
        return new class implements ApplyFilters, GetQueriedObject {
            public function applyFilters(string $hookName, mixed $value, mixed ...$args): mixed
            {
                return $value;
            }

            public function getQueriedObject(): \WP_Term|\WP_Post_Type|\WP_Post|\WP_User|null
            {
                return null;
            }
        };
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
