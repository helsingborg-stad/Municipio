<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\PostsList;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class ArchivePostsListFactoryTest extends TestCase
{
    #[TestDox('create() returns an instance of PostsList')]
    public function testCreateReturnsPostsList(): void
    {
        $data      = ['archiveProps' => (object)[]];
        $wpService = new FakeWpService(['addFilter' => true]);

        $factory = new ArchivePostsListFactory($wpService);
        $result  = $factory->create($data, []);

        $this->assertInstanceOf(PostsList::class, $result);
    }
}
