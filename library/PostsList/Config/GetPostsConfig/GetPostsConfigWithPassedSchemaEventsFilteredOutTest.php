<?php

declare(strict_types=1);

namespace Municipio\PostsList\Config\GetPostsConfig;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class GetPostsConfigWithPassedSchemaEventsFilteredOutTest extends TestCase
{
    #[TestDox('Returns null dateFrom when no Event post types are present')]
    public function testReturnsNullDateFromWhenNoEventPostTypesArePresent(): void
    {
        $innerConfig = new class extends DefaultGetPostsConfig {
            public function getPostTypes(): array
            {
                return ['post'];
            }

            public function getDateFrom(): null|string
            {
                return null;
            }
        };
        $schemaResolverMock = new class implements SchemaToPostTypeResolverInterface {
            public function resolve(string $schemaType): array
            {
                return $schemaType === 'Event' ? ['event'] : [];
            }
        };

        $config = new GetPostsConfigWithPassedSchemaEventsFilteredOut($innerConfig, $schemaResolverMock);

        static::assertNull($config->getDateFrom());
    }

    #[TestDox('Returns current date when Event post types are present and no dateFrom is set')]
    public function testReturnsCurrentDateWhenEventPostTypesArePresentAndNoDateFromIsSet(): void
    {
        $innerConfig = new class extends DefaultGetPostsConfig {
            public function getPostTypes(): array
            {
                return ['event'];
            }

            public function getDateFrom(): null|string
            {
                return null;
            }
        };
        $schemaResolverMock = new class implements SchemaToPostTypeResolverInterface {
            public function resolve(string $schemaType): array
            {
                return $schemaType === 'Event' ? ['event'] : [];
            }
        };

        $config = new GetPostsConfigWithPassedSchemaEventsFilteredOut($innerConfig, $schemaResolverMock);

        static::assertSame(date('Y-m-d'), $config->getDateFrom());
    }

    #[TestDox('Returns current date when Event post types are present and inner dateFrom is empty string')]
    public function testReturnsCurrentDateWhenEventPostTypesArePresentAndInnerDateFromIsEmptyString(): void
    {
        $innerConfig = new class extends DefaultGetPostsConfig {
            public function getPostTypes(): array
            {
                return ['event'];
            }

            public function getDateFrom(): null|string
            {
                return ' ';
            }
        };
        $schemaResolverMock = new class implements SchemaToPostTypeResolverInterface {
            public function resolve(string $schemaType): array
            {
                return $schemaType === 'Event' ? ['event'] : [];
            }
        };

        $config = new GetPostsConfigWithPassedSchemaEventsFilteredOut($innerConfig, $schemaResolverMock);

        static::assertSame(date('Y-m-d'), $config->getDateFrom());
    }

    #[TestDox('Returns inner dateFrom when Event post types are present and dateFrom is set')]
    public function testReturnsInnerDateFromWhenEventPostTypesArePresentAndDateFromIsSet(): void
    {
        $innerConfig = new class extends DefaultGetPostsConfig {
            public function getPostTypes(): array
            {
                return ['event'];
            }

            public function getDateFrom(): null|string
            {
                return '2023-01-01';
            }
        };
        $schemaResolverMock = new class implements SchemaToPostTypeResolverInterface {
            public function resolve(string $schemaType): array
            {
                return $schemaType === 'Event' ? ['event'] : [];
            }
        };

        $config = new GetPostsConfigWithPassedSchemaEventsFilteredOut($innerConfig, $schemaResolverMock);

        static::assertSame('2023-01-01', $config->getDateFrom());
    }
}
