<?php

namespace Municipio\Controller\Archive;

use Municipio\PostsList\PostsList;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use wpdb;
use WpService\Implementations\FakeWpService;

class ArchivePostsListFactoryTest extends TestCase
{
    #[TestDox('create() returns an instance of PostsList')]
    public function testCreateReturnsPostsList(): void
    {
        $data = ['archiveProps' => (object) []];
        $wpService = new FakeWpService(['addFilter' => true, 'getPostTypeArchiveLink' => '']);
        $wpdb = new wpdb('', '', '', '');

        $factory = new ArchivePostsListFactory($wpService, $wpdb);
        $result = $factory->create($data, []);

        $this->assertInstanceOf(PostsList::class, $result);
    }
}
