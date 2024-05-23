<?php

namespace Municipio\ExternalContent\SchemaObjectToWpPost\Helpers;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @testdox getSourceIdFromPostId() returns source ID
     */
    public function testGetSourceIdFromPostIdReturnsSourceId()
    {
        $helpers = new Helpers();
        $this->assertEquals(123, $helpers->getSourceIdFromPostId(-123456));
    }
}
