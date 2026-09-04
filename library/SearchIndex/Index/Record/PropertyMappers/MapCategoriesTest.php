<?php

declare(strict_types=1);

namespace Municipio\SearchIndex\Index\Record\PropertyMappers;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

/**
 * Tests mapping category taxonomy terms.
 */
class MapCategoriesTest extends TestCase
{
    #[TestDox('Maps category names and handles taxonomy errors')]
    public function testMapProperty(): void
    {
        $post = new \WP_Post([]);
        $post->ID = 42;
        $category = new \WP_Term([]);
        $category->name = 'News';

        $categories = (new MapCategories(new FakeWpService([
            'wpGetPostTerms' => [$category],
        ])))->mapProperty($post);
        $invalidCategories = (new MapCategories(new FakeWpService([
            'wpGetPostTerms' => new \WP_Error('invalid_taxonomy'),
        ])))->mapProperty($post);

        static::assertSame(['News'], $categories);
        static::assertSame([], $invalidCategories);
    }
}