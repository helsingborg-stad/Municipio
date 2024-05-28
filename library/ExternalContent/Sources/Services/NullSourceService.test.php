<?php

namespace Municipio\ExternalContent\Sources\Services;

use PHPUnit\Framework\TestCase;

class NullSourceServiceTest extends TestCase
{
    public function testReturnsNoInformation()
    {

        $nullSourceService = new NullSourceService();

        $this->assertNull($nullSourceService->getObject(0));
        $this->assertEmpty($nullSourceService->getObjects());
        $this->assertEmpty($nullSourceService->getPostType());
        $this->assertEquals('', $nullSourceService->getId());
    }
}
