<?php

namespace Municipio\ExternalContent\Sources\SourceDecorators;

use PHPUnit\Framework\TestCase;

class SourceServiceWithSourceIdTest extends TestCase
{
    /**
     * @testdox sets unique id if id is already in use
     */
    public function testSetsUniqueIdIfIdIsAlreadyInUse()
    {
        $inner                     = new Source('', '');
        $sourceServiceWithSourceId = new SourceServiceWithSourceId('foo', $inner);
        $sourceServiceWithSourceId = new SourceServiceWithSourceId('foo', $inner);
        $sourceServiceWithSourceId = new SourceServiceWithSourceId('foo', $inner);

        // Assert that no duplicates are in the idRegistry
        $this->assertCount(3, array_unique(SourceServiceWithSourceId::$idRegistry));
    }
}
