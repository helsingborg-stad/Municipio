<?php

declare(strict_types=1);

namespace Modularity;

use Override;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\AddFilter;

class SearchIndexTest extends TestCase
{
    public function testAddsRenderedModuleContent(): void
    {
        $searchIndex = new class (static::createWpService()) extends \Modularity\SearchIndex {
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
        $searchIndex = new class (static::createWpService()) extends \Modularity\SearchIndex {
            public function getRenderedPostModules(int $postId): string
            {
                return '';
            }
        };

        static::assertSame('Page content', $searchIndex->addModuleContent('Page content', 42));
    }

    public function testNormalizesModuleMarkupWithoutExecutableContent(): void
    {
        $searchIndex = new \Modularity\SearchIndex(static::createWpService());
        $normalizeText = new \ReflectionMethod($searchIndex, 'normalizeText');

        $result = $normalizeText->invoke(
            $searchIndex,
            '<style>.example { display: none; }</style><p>Module content</p><script>alert("ignored");</script>',
        );

        static::assertSame('Module content', $result);
    }

    public function testRemovesModulePostTypes(): void
    {
        static::assertSame(
            ['post', 'page'],
            (new \Modularity\SearchIndex(static::createWpService()))->removeModulePostTypes(['post', 'mod-text', 'page', 'mod-wpwidget']),
        );
    }

    private static function createWpService():AddFilter {
        return new class () implements AddFilter {
            public function addFilter(string $hookName, callable $callback, int $priority = 10, int $acceptedArgs = 1): true
            {
                return true;
            }
        };
    }
}