<?php

declare(strict_types=1);

namespace Modularity;

use PHPUnit\Framework\TestCase;

class SearchIndexTest extends TestCase
{
    public function testAddsRenderedModuleContent(): void
    {
        $searchIndex = new class () extends \Modularity\SearchIndex {
            public function getRenderedPostModules(int $postId): string
            {
                return $postId === 42 ? 'Module heading Module content' : '';
            }
        };

        static::assertSame(
            'Page content Module heading Module content',
            $searchIndex->addModuleContent('Page content', 42),
        );
    }

    public function testLeavesContentUnchangedWithoutModules(): void
    {
        $searchIndex = new class () extends \Modularity\SearchIndex {
            public function getRenderedPostModules(int $postId): string
            {
                return '';
            }
        };

        static::assertSame('Page content', $searchIndex->addModuleContent('Page content', 42));
    }

    public function testRemovesModulePostTypes(): void
    {
        static::assertSame(
            ['post', 'page'],
            (new \Modularity\SearchIndex())->removeModulePostTypes(['post', 'mod-text', 'page', 'mod-wpwidget']),
        );
    }
}