<?php

namespace Municipio\ExternalContent\WpPostArgsFromSchemaObject;

use Municipio\ExternalContent\Sources\SourceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\SchemaOrg\Thing;
use WpService\Implementations\FakeWpService;

class VerifyChecksumTest extends TestCase
{
    /**
     * @testdox if the checksum is the same as before, the post should not be updated.
     */
    public function testCreate()
    {
        /**
         * @var WpPostArgsFromSchemaObjectInterface|\PHPUnit\Framework\MockObject\MockObject $inner
         */
        $inner = $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
        $inner->method('create')->willReturn(['ID' => 123, 'meta_input' => ['checksum' => '321']]);

        $verifyChecksum = new VerifyChecksum($inner, new FakeWpService(['getPostMeta' => '321']));
        $postArgs       = $verifyChecksum->create(new Thing(), $this->getSourceMock());

        $this->assertEquals(-1, $postArgs['ID']);
    }

    /**
     * @testdox if the checksum is different from before, the post should be updated.
     */
    public function testCreateDifferentChecksum()
    {
        /**
         * @var WpPostArgsFromSchemaObjectInterface|\PHPUnit\Framework\MockObject\MockObject $inner
         */
        $inner = $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
        $inner->method('create')->willReturn(['ID' => 123, 'meta_input' => ['checksum' => '321']]);

        $verifyChecksum = new VerifyChecksum($inner, new FakeWpService(['getPostMeta' => '123']));
        $postArgs       = $verifyChecksum->create(new Thing(), $this->getSourceMock());

        $this->assertEquals(123, $postArgs['ID']);
    }

    /**
     * @testdox if the checksum is not set, the post should be updated.
     */
    public function testCreateNoChecksum()
    {
        /**
         * @var WpPostArgsFromSchemaObjectInterface|\PHPUnit\Framework\MockObject\MockObject $inner
         */
        $inner = $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
        $inner->method('create')->willReturn(['ID' => 123]);

        $verifyChecksum = new VerifyChecksum($inner, new FakeWpService(['getPostMeta' => '123']));
        $postArgs       = $verifyChecksum->create(new Thing(), $this->getSourceMock());

        $this->assertEquals(123, $postArgs['ID']);
    }

    /**
     * @testdox if the ID is not set, the post should be updated.
     */
    public function testCreateNoId()
    {
        /**
         * @var WpPostArgsFromSchemaObjectInterface|\PHPUnit\Framework\MockObject\MockObject $inner
         */
        $inner = $this->createMock(WpPostArgsFromSchemaObjectInterface::class);
        $inner->method('create')->willReturn(['meta_input' => ['checksum' => '321']]);

        $verifyChecksum = new VerifyChecksum($inner, new FakeWpService(['getPostMeta' => '321']));
        $postArgs       = $verifyChecksum->create(new Thing(), $this->getSourceMock());

        $this->assertArrayNotHasKey('ID', $postArgs);
    }

    private function getSourceMock(): SourceInterface|MockObject
    {
        return $this->createMock(SourceInterface::class);
    }
}
