<?php

namespace Municipio\ExternalContent\SyncHandler;

use Municipio\ExternalContent\SourceReaders\SourceReaderInterface;
use Municipio\ExternalContent\WpPostArgsFromSchemaObject\WpPostArgsFromSchemaObjectInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Schema;
use WpService\Contracts\WpInsertPost;
use WpService\Implementations\FakeWpService;

class SyncHandlerTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testClassCanBeInstantiated()
    {
        $sourceReader  = $this->getSourceReaderMock();
        $wpPostFactory = $this->getWpPostFactoryMock();
        $wpService     = $this->getWpServiceMock();

        $syncHandler = new SyncHandler($sourceReader, $wpPostFactory, $wpService);

        $this->assertInstanceOf(SyncHandler::class, $syncHandler);
    }

    /**
     * @testdox inserts posts
     */
    public function testInsertsPosts()
    {
        $sourceReader  = $this->getSourceReaderMock();
        $wpPostFactory = $this->getWpPostFactoryMock();
        $wpService     = $this->getWpServiceMock();
        $syncHandler   = new SyncHandler($sourceReader, $wpPostFactory, $wpService);
        $sourceData    = [ Schema::thing() ];
        $wpPostArgs    = ['title' => 'Title 1'];

        $sourceReader->method('getSourceData')->willReturn($sourceData);
        $wpPostFactory->method('transform')->willReturn($wpPostArgs);

        $syncHandler->sync();
        $firstParamOfFirstCallToWpInsertPost = $wpService->methodCalls['wpInsertPost'][0][0];

        $this->assertSame($wpPostArgs, $firstParamOfFirstCallToWpInsertPost);
    }

    private function getSourceReaderMock(): SourceReaderInterface|MockObject
    {
        return $this->createMock(SourceReaderInterface::class);
    }

    private function getWpPostFactoryMock(): WpPostArgsFromSchemaObjectInterface|MockObject
    {
        return $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
    }

    private function getWpServiceMock(): WpInsertPost
    {
        return new FakeWpService(['wpInsertPost' => 1]);
    }
}
