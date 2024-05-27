<?php

namespace Municipio\ExternalContent\Sources\Services;

use PHPUnit\Framework\TestCase;

class SourceServiceWithSourceIdTest extends TestCase
{
    /**
     * @testdox sets unique id if id is already in use
     */
    public function testSetsUniqueIdIfIdIsAlreadyInUse()
    {
        $sourceServiceWithSourceId = new SourceServiceWithSourceId('foo');
        $sourceServiceWithSourceId = new SourceServiceWithSourceId('foo');
        $sourceServiceWithSourceId = new SourceServiceWithSourceId('foo');

        // Assert that no duplicates are in the idRegistry
        $this->assertCount(3, array_unique(SourceServiceWithSourceId::$idRegistry));
    }
}
