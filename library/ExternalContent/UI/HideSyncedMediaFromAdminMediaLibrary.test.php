<?php

namespace Municipio\ExternalContent\UI;

use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class HideSyncedMediaFromAdminMediaLibraryTest extends TestCase
{
    /**
     * @testdox modifies query if is admin and query is for attachments
     */
    public function testPreGetPostsModifiesQueryIfIsAdminAndQueryIsForAttachments()
    {
        $metaKey = 'some-meta-key';

        $wpService = new FakeWpService(['isAdmin' => true]);
        $query     = WpMockFactory::createWpQuery(['query' => ['post_type' => 'attachment'], 'query_vars' => []]);

        $hideSyncedMediaFromAdminMediaLibrary = new HideSyncedMediaFromAdminMediaLibrary($metaKey, $wpService);
        $hideSyncedMediaFromAdminMediaLibrary->preGetPosts($query);

        $this->assertEquals(
            [
                [
                    'key'     => $metaKey,
                    'compare' => 'NOT EXISTS',
                ],
            ],
            $query->query_vars['meta_query']
        );
    }
}
