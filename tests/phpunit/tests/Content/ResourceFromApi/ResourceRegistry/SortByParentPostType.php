<?php

namespace Municipio\Tests\Content\ResourceFromApi\ResourceRegistry;

use Mockery;
use Municipio\Content\ResourceFromApi\ResourceInterface;
use WP_Mock\Tools\TestCase;

/**
 * Class SortByParentPostTypeTest
 */
class SortByParentPostTypeTest extends TestCase
{
    /**
     * @testdox Resources with parent post types are sorted to last in array.
     */
    public function testResourcesWithParentPostTypesSortedToLastInArray()
    {

        $withoutParentPostType = Mockery::mock(ResourceInterface::class);
        $withoutParentPostType->shouldReceive('getName')->andReturn('without-parent');
        $withoutParentPostType->shouldReceive('getArguments')->andReturn([]);

        $withParentPostType = Mockery::mock(ResourceInterface::class);
        $withParentPostType->shouldReceive('getName')->andReturn('with-parent');
        $withParentPostType->shouldReceive('getArguments')->andReturn([ 'parent_post_types' => ['foo']]);

        $resources = [
            $withParentPostType,
            $withoutParentPostType,
        ];

        $sorter = new \Municipio\Content\ResourceFromApi\ResourceRegistry\SortByParentPostType();
        $sorted = $sorter->sortByParentPostType($resources);

        $this->assertEquals('without-parent', array_shift($sorted)->getName());
        $this->assertEquals('with-parent', array_shift($sorted)->getName());
    }
}
