<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping the highest parent title.
 */
class MapTopMostParentTest extends TestCase
{
    #[TestDox('Maps the highest ancestor title')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;
        $post->post_title = 'Child page';
        $parent = new \WP_Post([]);
        $parent->ID = 5;
        $parent->post_title = 'Root page';
        $wpService = new FakeWpService([
            'getPostAncestors' => [10, 5],
            'getPost' => $parent,
        ]);

        $result = (new MapTopMostParent($wpService))->mapProperty($post);

        static::assertSame('Root page', $result);
    }
}