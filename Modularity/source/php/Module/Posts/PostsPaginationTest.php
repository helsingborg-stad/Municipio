<?php

declare(strict_types=1);

namespace Modularity\Module\Posts;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

// @mago-expect lint:function-name
function __(string $text, string $domain = 'default'): string
{
    return $text;
}

/**
 * Mirrors WordPress: without a URL, the current request URI is used.
 */
function add_query_arg(string $key, mixed $value): string
{
    return $_SERVER['REQUEST_URI'] . '&' . $key . '=' . $value;
}

/**
 * Mirrors WordPress: without a URL, the current request URI is used.
 */
function remove_query_arg(string $key): string
{
    return (string) $_SERVER['REQUEST_URI'];
}

class PostsPaginationTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_URI']);
    }

    /**
     * Pagination is cached with the module, so its links must not carry one visitor's URL to the next.
     *
     * @see https://github.com/helsingborg-stad/Municipio/pull/2437#discussion_r4709642752
     */
    #[TestDox('getPaginationArguments() builds links that do not depend on the current request')]
    public function testPaginationLinksDoNotDependOnTheCurrentRequest(): void
    {
        $_SERVER['REQUEST_URI'] = '/news/article/?s=search+term&mod-posts-99-page=4';

        static::assertSame(
            ['?mod-posts-12-page=1', '?mod-posts-12-page=2', '?mod-posts-12-page=3'],
            array_column($this->getPaginationArguments(maxNumPages: 3, currentPage: 1)['list'], 'href'),
        );
    }

    #[TestDox('getPaginationArguments() returns no pagination for a single page')]
    public function testNoPaginationForASinglePage(): void
    {
        static::assertSame([], $this->getPaginationArguments(maxNumPages: 1, currentPage: 1));
    }

    private function getPaginationArguments(int $maxNumPages, int $currentPage): array
    {
        $posts = (new \ReflectionClass(Posts::class))->newInstanceWithoutConstructor();
        $posts->slug = 'posts';
        $posts->ID = 12;

        return (new \ReflectionMethod(Posts::class, 'getPaginationArguments'))->invoke($posts, $maxNumPages, $currentPage);
    }
}
