<?php

declare(strict_types=1);

namespace Modularity\Helper\FragmentCache;

use Municipio\TestUtils\InMemoryObjectCache;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class FragmentStoreTest extends TestCase
{
    private InMemoryObjectCache $objectCache;
    private FragmentStore $store;

    protected function setUp(): void
    {
        $this->objectCache = new InMemoryObjectCache();
        $this->store = new FragmentStore($this->objectCache, 'modules');
    }

    #[TestDox('get() returns null when no fragment is stored')]
    public function testGetReturnsNullWhenNothingIsStored(): void
    {
        static::assertNull($this->store->get(42, 'hash-a'));
    }

    #[TestDox('get() returns a stored fragment with its markup and assets')]
    public function testGetReturnsAStoredFragment(): void
    {
        $this->store->set(42, 'hash-a', new Fragment('<p>A</p>', ['components' => ['collection']]), 60);

        static::assertEquals(new Fragment('<p>A</p>', ['components' => ['collection']]), $this->store->get(42, 'hash-a'));
    }

    #[TestDox('set() writes only the given fragment, with its own TTL')]
    public function testSetWritesOnlyTheGivenFragmentWithItsOwnTtl(): void
    {
        $this->store->set(42, 'hash-a', new Fragment('<p>A</p>', []), 60);
        $this->objectCache->clearWrites();

        $this->store->set(42, 'hash-b', new Fragment('<p>B</p>', []), 120);

        $writes = $this->objectCache->getWrites();
        static::assertCount(1, $writes);
        static::assertSame(120, $writes[0]['expire']);
        static::assertStringContainsString('<p>B</p>', serialize($writes[0]['data']));
    }

    #[TestDox('clear() makes every stored fragment of the post miss')]
    public function testClearMakesEveryFragmentOfThePostMiss(): void
    {
        $this->store->set(42, 'hash-a', new Fragment('<p>A</p>', []), 60);
        $this->store->set(42, 'hash-b', new Fragment('<p>B</p>', []), 60);

        $this->store->clear(42);

        static::assertNull($this->store->get(42, 'hash-a'));
        static::assertNull($this->store->get(42, 'hash-b'));
    }

    #[TestDox('clear() leaves fragments of other posts untouched')]
    public function testClearLeavesOtherPostsUntouched(): void
    {
        $this->store->set(7, 'hash-a', new Fragment('<p>Other</p>', []), 60);

        $this->store->clear(42);

        static::assertEquals(new Fragment('<p>Other</p>', []), $this->store->get(7, 'hash-a'));
    }

    #[TestDox('set() after clear() stores a fragment that can be read again')]
    public function testSetAfterClearIsReadable(): void
    {
        $this->store->set(42, 'hash-a', new Fragment('<p>Old</p>', []), 60);
        $this->store->clear(42);

        $this->store->set(42, 'hash-a', new Fragment('<p>New</p>', []), 60);

        static::assertEquals(new Fragment('<p>New</p>', []), $this->store->get(42, 'hash-a'));
    }

    #[TestDox('get() does not serve old fragments when the version key has been evicted')]
    public function testGetDoesNotServeOldFragmentsWhenTheVersionKeyIsEvicted(): void
    {
        $this->store->set(42, 'hash-a', new Fragment('<p>A</p>', []), 60);
        $this->store->clear(42);
        $this->store->set(42, 'hash-a', new Fragment('<p>Stale</p>', []), 60);

        $this->objectCache->wpCacheDelete('42:version', 'modules');

        static::assertNull($this->store->get(42, 'hash-a'));
    }
}
