<?php

namespace Municipio\PostsList\GetPosts\PostsListConfigToGetPostsArgs;

use Municipio\PostsList\Config\AppearanceConfig\DefaultAppearanceConfig;
use Municipio\PostsList\Config\GetPostsConfig\DefaultGetPostsConfig;
use Municipio\PostsList\Config\GetPostsConfig\OrderDirection;
use PHPUnit\Framework\TestCase;

/**
 * Test to verify ApplyOrder can work independently of ApplyDate
 */
class ApplyOrderDecouplingTest extends TestCase
{
    public function testApplyOrderWorksIndependentlyOfApplyDate(): void
    {
        // Arrange: Create config that would normally require ApplyDate to run first
        $getPostsConfig = new class () extends DefaultGetPostsConfig {
            public function getOrderBy(): string
            {
                return 'custom_date_field'; // Custom field that needs meta query
            }

            public function getOrder(): OrderDirection
            {
                return OrderDirection::DESC;
            }

            public function getDateSource(): string
            {
                return 'custom_date_field'; // Same as orderBy - would need date clause
            }

            public function getDateFrom(): ?string
            {
                return '2023-01-01';
            }

            public function getDateTo(): ?string
            {
                return '2023-12-31';
            }
        };

        $appearanceConfig = new class () extends DefaultAppearanceConfig {
            public function getDateSource(): string
            {
                return 'custom_date_field';
            }
        };

        // Act: Apply order WITHOUT running ApplyDate first (empty args)
        $applier = new ApplyOrder($appearanceConfig);
        $args = $applier->apply($getPostsConfig, []);

        // Assert: ApplyOrder should have created the needed date clause itself
        $this->assertArrayHasKey('meta_query', $args, 'ApplyOrder should create meta_query when needed');
        $this->assertArrayHasKey(MetaQueryKeys::DATE_CLAUSE, $args['meta_query'], 'Date clause should exist');
        $this->assertArrayHasKey('orderby', $args, 'Order by should be set');
        
        // Verify the date clause was properly created
        $dateClause = $args['meta_query'][MetaQueryKeys::DATE_CLAUSE];
        $this->assertEquals('custom_date_field', $dateClause['key']);
        $this->assertEquals('BETWEEN', $dateClause['compare']);
        $this->assertEquals('DATETIME', $dateClause['type']);
        
        // Verify ordering references the date clause
        $this->assertEquals([MetaQueryKeys::DATE_CLAUSE => 'DESC'], $args['orderby']);
    }

    public function testApplyOrderDoesntDuplicateDateClause(): void
    {
        // Arrange: Simulate ApplyDate already ran and created the date clause
        $existingArgs = [
            'meta_query' => [
                MetaQueryKeys::DATE_CLAUSE => [
                    'key' => 'custom_date_field',
                    'value' => ['2023-01-01 00:00:00', '2023-12-31 23:59:59'],
                    'compare' => 'BETWEEN',
                    'type' => 'DATETIME',
                ],
                'some_other_clause' => [
                    'key' => 'other_field',
                    'value' => 'other_value',
                ]
            ]
        ];

        $getPostsConfig = new class () extends DefaultGetPostsConfig {
            public function getOrderBy(): string
            {
                return 'custom_date_field';
            }

            public function getOrder(): OrderDirection
            {
                return OrderDirection::ASC;
            }

            public function getDateSource(): string
            {
                return 'custom_date_field';
            }
        };

        $appearanceConfig = new class () extends DefaultAppearanceConfig {
            public function getDateSource(): string
            {
                return 'custom_date_field';
            }
        };

        // Act: Apply order with existing date clause
        $applier = new ApplyOrder($appearanceConfig);
        $args = $applier->apply($getPostsConfig, $existingArgs);

        // Assert: Should not duplicate the date clause
        $this->assertCount(2, $args['meta_query'], 'Should not add duplicate date clause');
        $this->assertArrayHasKey(MetaQueryKeys::DATE_CLAUSE, $args['meta_query']);
        $this->assertArrayHasKey('some_other_clause', $args['meta_query']);
        
        // Verify ordering still works
        $this->assertEquals([MetaQueryKeys::DATE_CLAUSE => 'ASC'], $args['orderby']);
    }
}