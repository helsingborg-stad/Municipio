<?php

namespace Municipio\ExternalContent\UI;

use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WP_Query;
use WpService\Implementations\FakeWpService;

class HideSyncedMediaFromAdminMediaLibraryTest extends TestCase
{
    /**
     * @testdox modifies query if is admin and query is for attachments
     */
    public function testPreGetPostsModifiesQueryIfIsAdminAndQueryIsForAttachments()
    {
        $metaKey = 'some-meta-key';

        $wpService           = new FakeWpService(['isAdmin' => true]);
        $wpQuery             = new WP_Query();
        $wpQuery->query_vars = [];
        $wpQuery->query      = ['post_type' => 'attachment'];

        $hideSyncedMediaFromAdminMediaLibrary = new HideSyncedMediaFromAdminMediaLibrary($metaKey, $wpService);
        $hideSyncedMediaFromAdminMediaLibrary->preGetPosts($wpQuery);

        $this->assertEquals(
            [
                [
                    'key'     => $metaKey,
                    'compare' => 'NOT EXISTS',
                ],
            ],
            $wpQuery->query_vars['meta_query']
        );
    }
}
