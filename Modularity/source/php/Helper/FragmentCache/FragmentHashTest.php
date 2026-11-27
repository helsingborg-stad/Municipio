<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FragmentHashTest extends TestCase
{
    #[TestDox('create() gives the same hash for the same input')]
    public function testCreateIsStableForTheSameInput(): void
    {
        $hash = new FragmentHash();

        static::assertSame(
            $hash->create(['module' => 123, 'placement' => 'content-area'], ['mod-posts-123-page' => '2']),
            $hash->create(['module' => 123, 'placement' => 'content-area'], ['mod-posts-123-page' => '2']),
        );
    }

    #[TestDox('create() gives a different hash for different module input')]
    public function testCreateDiffersForDifferentModuleInput(): void
    {
        $hash = new FragmentHash();

        static::assertNotSame($hash->create('module-a', null), $hash->create('module-b', null));
    }

    #[TestDox('create() gives a different hash for different query vars')]
    public function testCreateDiffersForDifferentQueryVars(): void
    {
        $hash = new FragmentHash();

        static::assertNotSame(
            $hash->create('module', ['mod-posts-123-page' => '1']),
            $hash->create('module', ['mod-posts-123-page' => '2']),
        );
    }

    #[TestDox('create() gives a short hash that is safe to use in a cache key')]
    public function testCreateGivesAShortKeySafeHash(): void
    {
        static::assertMatchesRegularExpression('/^[a-z0-9-]+$/', (string) (new FragmentHash())->create(['a' => 1], ['b' => 2]));
    }
}
