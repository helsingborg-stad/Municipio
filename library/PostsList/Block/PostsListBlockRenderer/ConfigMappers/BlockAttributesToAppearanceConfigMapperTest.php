<?php

declare(strict_types=1);

namespace Municipio\PostsList\Block\PostsListBlockRenderer\ConfigMappers;

use Municipio\PostsList\Config\AppearanceConfig\DateFormat;
use Municipio\PostsList\Config\AppearanceConfig\PostDesign;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class BlockAttributesToAppearanceConfigMapperTest extends TestCase {
    #[TestDox('can map from default block attributes')]
    public function testCanMapFromDefaultBlockAttributes(): void {
        $attributes = [
            'numberOfColumns' => 3,
            'design' => 'card',
            'dateFormat' => 'date',
            'dateSource' => 'post_date',
            'dateFilterEnabled' => false,
            'order' => 'desc',
            'orderBy' => 'date',
            'paginationEnabled' => true,
            'postsPerPage' => 12,
            'postType' => 'page',
            'textSearchEnabled' => false,
        ];

        $mapper = new BlockAttributesToAppearanceConfigMapper();
        $config = $mapper->map($attributes);

        static::assertSame(3, $config->getNumberOfColumns());
        static::assertSame(PostDesign::CARD, $config->getDesign());
        static::assertSame(DateFormat::from('date'), $config->getDateFormat());
        static::assertSame('post_date', $config->getDateSource());
    }
}