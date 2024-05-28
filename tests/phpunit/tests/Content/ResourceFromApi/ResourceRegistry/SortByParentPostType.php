<?php

namespace Municipio\Tests\Content\ResourceFromApi\ResourceRegistry;

use Mockery;
use Municipio\Content\ResourceFromApi\ResourceInterface;
use WP_Mock\Tools\TestCase;

/**
 * Class SortByParentPostTypeTest
 * @group wp_mock
 */
class SortByParentPostTypeTest extends TestCase
{
    /**
     * @testdox Resources with parent post types are sorted to last in array.
     * @dataProvider getEmptyParentPostTypes
     */
    public function testResourcesWithParentPostTypesSortedToLastInArray($parentPostTypes)
    {
        $withoutParentPostType = Mockery::mock(ResourceInterface::class);
        $withoutParentPostType->shouldReceive('getName')->andReturn('without-parent');
        $withoutParentPostType->shouldReceive('getArguments')->andReturn([ 'parent_post_types' => $parentPostTypes]);

        $withParentPostType = Mockery::mock(ResourceInterface::class);
        $withParentPostType->shouldReceive('getName')->andReturn('with-parent');
        $withParentPostType->shouldReceive('getArguments')->andReturn([ 'parent_post_types' => ['foo']]);

        $resources = [
            $withParentPostType,
            $withoutParentPostType
        ];

        $sorter = new \Municipio\Content\ResourceFromApi\ResourceRegistry\SortByParentPostType();
        $sorted = $sorter->sortByParentPostType($resources);

        $this->assertEquals('without-parent', $sorted[0]->getName());
        $this->assertEquals('with-parent', $sorted[1]->getName());
    }

    /**
     * Parent post types datasets for testing empty values.
     */
    public function getEmptyParentPostTypes()
    {
        return [ [[]], [[""]], [[null]]];
    }
}
