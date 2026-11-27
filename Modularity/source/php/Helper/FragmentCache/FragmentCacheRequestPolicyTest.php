<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FragmentCacheRequestPolicyTest extends TestCase
{
    #[TestDox('allowsCaching() is true for $_dataName')]
    #[DataProvider('cacheableRequestProvider')]
    public function testAllowsCachingForRequestsWithoutParametersThatChangeMarkup(array $queryParams): void
    {
        static::assertTrue((new FragmentCacheRequestPolicy($queryParams, ['s', 'paged']))->allowsCaching());
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function cacheableRequestProvider(): array
    {
        return [
            'a request without parameters' => [[]],
            'tracking parameters that WordPress does not register' => [['utm_source' => 'newsletter', 'fbclid' => 'abc']],
        ];
    }

    #[TestDox('allowsCaching() is false for $_dataName')]
    #[DataProvider('uncacheableRequestProvider')]
    public function testDisallowsCachingForRequestsWithParametersThatMayChangeMarkup(array $queryParams): void
    {
        static::assertFalse((new FragmentCacheRequestPolicy($queryParams, ['s', 'paged']))->allowsCaching());
    }

    /**
     * @return array<string, array{array<string, mixed>}>
     */
    public static function uncacheableRequestProvider(): array
    {
        return [
            'a module pagination parameter' => [['mod-posts-12-page' => '2']],
            'a mod-* parameter no module uses' => [['mod-text-123-unused' => 'random']],
            'a mod-* parameter in upper case' => [['MOD-posts-12-page' => '2']],
            'a registered query var' => [['s' => 'search term']],
            'a registered query var next to tracking parameters' => [['utm_source' => 'newsletter', 'paged' => '2']],
        ];
    }
}
