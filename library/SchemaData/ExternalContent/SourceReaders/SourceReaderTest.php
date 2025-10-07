<?php

namespace Municipio\SchemaData\ExternalContent\SourceReaders;

use PHPUnit\Framework\TestCase;

class SourceReaderTest extends TestCase
{
    #[TestDox('class can be instantiated')]
    public function testCanBeInstantiated()
    {
        $sourceReader = new SourceReader();
        $this->assertInstanceOf(SourceReader::class, $sourceReader);
    }

    #[TestDox('getSourceData() returns an array')]
    public function testGetSourceDataReturnsAnArray()
    {
        $sourceReader = new SourceReader();
        $this->assertIsArray($sourceReader->getSourceData());
    }
}
