<?php

declare(strict_types=1);

namespace Municipio\Styleguide\Customize;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class ResolvePostTypeScopeTest extends TestCase
{
    #[TestDox('returns null for an empty post type')]
    public function testResolveReturnsNullForEmptyPostType(): void
    {
        $resolver = new ResolvePostTypeScope();

        $result = $resolver->resolve('');

        static::assertNull($result);
    }

    #[TestDox('returns a post type scope for a valid post type')]
    public function testResolveReturnsPostTypeScope(): void
    {
        $resolver = new ResolvePostTypeScope();

        $result = $resolver->resolve('news');

        static::assertSame('s-post-type-news;', $result);
    }

    #[TestDox('returns shared and single scopes for single views')]
    public function testResolveReturnsSharedAndSingleScopes(): void
    {
        $resolver = new ResolvePostTypeScope();

        $result = $resolver->resolve('news', true, false);

        static::assertSame('s-post-type-news; s-post-type-news-single;', $result);
    }

    #[TestDox('returns shared and archive scopes for archive views')]
    public function testResolveReturnsSharedAndArchiveScopes(): void
    {
        $resolver = new ResolvePostTypeScope();

        $result = $resolver->resolve('news', false, true);

        static::assertSame('s-post-type-news; s-post-type-news-archive;', $result);
    }

    #[TestDox('returns shared single and archive scopes when both are requested')]
    public function testResolveReturnsAllMatchingScopes(): void
    {
        $resolver = new ResolvePostTypeScope();

        $result = $resolver->resolve('news', true, true);

        static::assertSame('s-post-type-news; s-post-type-news-single; s-post-type-news-archive;', $result);
    }

    #[TestDox('sanitizes the post type before building the scope')]
    public function testResolveSanitizesThePostType(): void
    {
        $resolver = new ResolvePostTypeScope();

        $result = $resolver->resolve('custom post/type', true, false);

        static::assertSame('s-post-type-custom-post-type; s-post-type-custom-post-type-single;', $result);
    }
}
