<?php

namespace Municipio\ExternalContent\Sources\SourceDecorators;

use Municipio\ExternalContent\Sources\Source;
use PHPUnit\Framework\TestCase;

class SourceServiceWithSourceIdTest extends TestCase
{
    /**
     * @testdox sets unique id if id is already in use
     */
    public function testSetsUniqueIdIfIdIsAlreadyInUse()
    {
        $inner                     = new Source('', '');
        $sourceServiceWithSourceId = new SourceServiceWithSourceId($inner);
        $sourceServiceWithSourceId = new SourceServiceWithSourceId($inner);
        $sourceServiceWithSourceId = new SourceServiceWithSourceId($inner);

        // Assert that no duplicates are in the idRegistry
        $this->assertCount(3, array_unique(SourceServiceWithSourceId::$idRegistry));
    }
}
