<?php

namespace Municipio\ExternalContent\Sync;

use Municipio\TestUtils\WpMockFactory;
use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class PruneTermsNoLongerInUseTest extends TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(class_exists('Municipio\ExternalContent\Sync\PruneAttachmentsNoLongerInUse'));
    }

    /**
     * @testdox Deletes attachments that are not longer in use.
     */
    public function testDeletesAttachmentsNotInUse()
    {
        $attachmentId = 1;
        $wpService    = new FakeWpService();
        $wpdb         = WpMockFactory::createWpdb([
            'postmeta'    => 'postmeta',
            'prepare'     => fn($query, ...$args) => $query,
            'get_results' => fn() => [(object)['post_id' => $attachmentId]]]);

        $sut = new PruneAttachmentsNoLongerInUse($wpService, $wpdb);
        $sut->sync();

        $this->assertArrayHasKey('deleteAttachment', $wpService->methodCalls);
        $this->assertCount(1, $wpService->methodCalls['deleteAttachment']);
        $this->assertEquals($attachmentId, $wpService->methodCalls['deleteAttachment'][0][0]);
        $this->assertTrue($wpService->methodCalls['deleteAttachment'][0][1]); // Force delete
    }
}
