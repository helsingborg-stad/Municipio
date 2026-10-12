<?php

declare(strict_types=1);


namespace Municipio\PostsList\Block\PostsListEventsBlockRender;

use Municipio\SchemaData\Utils\SchemaToPostTypesResolver\SchemaToPostTypeResolverInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\RenderBlock;

class PostsListEventsBlockRenderTest extends TestCase
{
    #[TestDox('throws if no post types are found for schema type "Event"')]
    public function testThrowsIfNoPostTypesFound(): void
    {
        $wpService = new class implements RenderBlock {
            public function renderBlock(array $parsedBlock): string
            {
                throw new \Exception('Not implemented');
            }
        };
        $postTypeResolver = new class implements SchemaToPostTypeResolverInterface {
            public function resolve(string $schemaType): array
            {
                return [];
            }
        };

        try {
            $blockRender = new PostsListEventsBlockRender($wpService, $postTypeResolver);
            $blockRender->render([]);
        } catch (\Exception $e) {
            static::assertSame('No post types found for schema type "Event".', $e->getMessage());
            return;
        }

        static::fail('Expected exception not thrown.');
    }

    #[TestDox('calls renderBlock on the WP service with the correct attributes')]
    public function testCallsRenderBlockWithCorrectAttributes(): void
    {
        $wpService = new class implements RenderBlock {
            public array $receivedBlock = [];

            public function renderBlock(array $parsedBlock): string
            {
                $this->receivedBlock = $parsedBlock;
                return 'rendered';
            }
        };
        $postTypeResolver = new class implements SchemaToPostTypeResolverInterface {
            public function resolve(string $schemaType): array
            {
                return ['event'];
            }
        };

        $blockRender = new PostsListEventsBlockRender($wpService, $postTypeResolver);
        $blockRender->render([]);

        static::assertSame('municipio/posts-list-block', $wpService->receivedBlock['blockName']);
        static::assertSame('event', $wpService->receivedBlock['attrs']['postType']);
        static::assertSame('startDate', $wpService->receivedBlock['attrs']['dateSource']);
        static::assertSame('asc', $wpService->receivedBlock['attrs']['order']);
    }

    #[TestDox('merges custom attributes with default attributes')]
    public function testMergesCustomAttributesWithDefaultAttributes(): void
    {
        $wpService = new class implements RenderBlock {
            public array $receivedBlock = [];

            public function renderBlock(array $parsedBlock): string
            {
                $this->receivedBlock = $parsedBlock;
                return 'rendered';
            }
        };
        $postTypeResolver = new class implements SchemaToPostTypeResolverInterface {
            public function resolve(string $schemaType): array
            {
                return ['event'];
            }
        };

        $blockRender = new PostsListEventsBlockRender($wpService, $postTypeResolver);
        $blockRender->render([
            'dateSource' => 'customDate',
            'order' => 'desc',
        ]);

        static::assertSame('municipio/posts-list-block', $wpService->receivedBlock['blockName']);
        static::assertSame('event', $wpService->receivedBlock['attrs']['postType']);
        static::assertSame('customDate', $wpService->receivedBlock['attrs']['dateSource']);
        static::assertSame('desc', $wpService->receivedBlock['attrs']['order']);
    }
}
