<?php

namespace Municipio\ExternalContent\SourceReaders;

use PHPUnit\Framework\TestCase;

class SourceReaderTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $sourceReader = new SourceReader();
        $this->assertInstanceOf(SourceReader::class, $sourceReader);
    }

    /**
     * @testdox getSourceData() returns an array
     */
    public function testGetSourceDataReturnsAnArray()
    {
        $sourceReader = new SourceReader();
        $this->assertIsArray($sourceReader->getSourceData());
    }
}
