<?php

namespace Municipio\ExternalContent\SyncHandler\WpInsertPost;

use PHPUnit\Framework\TestCase;
use WpService\Implementations\FakeWpService;

class InsertPostOnlyIfSyncIsNotAlreadyInProgressTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $wpService                                  = new FakeWpService([]);
        $insertPostOnlyIfSyncIsNotAlreadyInProgress = new InsertPostOnlyIfSyncIsNotAlreadyInProgress($wpService);

        $this->assertInstanceOf(InsertPostOnlyIfSyncIsNotAlreadyInProgress::class, $insertPostOnlyIfSyncIsNotAlreadyInProgress);
    }

    /**
     * @testdox does not insert post if preventSync is true
     */
    public function testDoesNotInsertPostIfPreventSyncIsTrue()
    {
        $wpService                                  = new FakeWpService([]);
        $insertPostOnlyIfSyncIsNotAlreadyInProgress = new InsertPostOnlyIfSyncIsNotAlreadyInProgress($wpService);

        $postarr = [ 'meta_input' => [ 'schemaData' => [ '@preventSync' => true ] ] ];

        $this->assertSame(0, $insertPostOnlyIfSyncIsNotAlreadyInProgress->wpInsertPost($postarr));
    }

    /**
     * @testdox inserts post if preventSync is false
     */
    public function testInsertsPostIfPreventSyncIsFalse()
    {
        $wpService                                  = new FakeWpService(['wpInsertPost' => 1]);
        $insertPostOnlyIfSyncIsNotAlreadyInProgress = new InsertPostOnlyIfSyncIsNotAlreadyInProgress($wpService);

        $postarr = [ 'meta_input' => [ 'schemaData' => [ '@preventSync' => false ] ] ];

        $this->assertSame(1, $insertPostOnlyIfSyncIsNotAlreadyInProgress->wpInsertPost($postarr));
    }

    /**
     * @testdox inserts post if preventSync is not set
     */
    public function testInsertsPostIfPreventSyncIsNotSet()
    {
        $wpService                                  = new FakeWpService(['wpInsertPost' => 1]);
        $insertPostOnlyIfSyncIsNotAlreadyInProgress = new InsertPostOnlyIfSyncIsNotAlreadyInProgress($wpService);

        $postarr = [ 'meta_input' => [ 'schemaData' => [] ] ];

        $this->assertSame(1, $insertPostOnlyIfSyncIsNotAlreadyInProgress->wpInsertPost($postarr));
    }
}
