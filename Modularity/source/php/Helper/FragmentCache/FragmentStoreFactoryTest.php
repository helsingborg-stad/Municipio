<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use Municipio\TestUtils\InMemoryObjectCache;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Contracts\GetCurrentBlogId;
use WpService\Contracts\IsMultisite;

class FragmentStoreFactoryTest extends TestCase
{
    #[TestDox('create() stores fragments in the "modules" group on a single site')]
    public function testCreateUsesTheModulesGroupOnASingleSite(): void
    {
        $objectCache = new InMemoryObjectCache();

        $this->createFactory($objectCache, isMultisite: false, blogId: 1)->create()->set(42, 'hash', new Fragment('<p>A</p>', []), 60);

        static::assertNotEmpty($objectCache->getGroup('modules'));
    }

    #[TestDox('create() stores fragments in a group per site on a multisite')]
    public function testCreateUsesAGroupPerSiteOnAMultisite(): void
    {
        $objectCache = new InMemoryObjectCache();

        $this->createFactory($objectCache, isMultisite: true, blogId: 3)->create()->set(42, 'hash', new Fragment('<p>A</p>', []), 60);

        static::assertNotEmpty($objectCache->getGroup('modules-3'));
        static::assertEmpty($objectCache->getGroup('modules'));
    }

    // @mago-expect lint:no-boolean-flag-parameter
    private function createFactory(InMemoryObjectCache $objectCache, bool $isMultisite, int $blogId): FragmentStoreFactory
    {
        $site = new class($isMultisite, $blogId) implements IsMultisite, GetCurrentBlogId {
            public function __construct(
                private readonly bool $isMultisite,
                private readonly int $blogId,
            ) {}

            public function isMultisite(): bool
            {
                return $this->isMultisite;
            }

            public function getCurrentBlogId(): int
            {
                return $this->blogId;
            }
        };

        return new FragmentStoreFactory($objectCache, $site);
    }
}
