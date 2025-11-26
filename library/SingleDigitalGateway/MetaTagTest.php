<?php

namespace Municipio\SingleDigitalGateway;

use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

class MetaTagTest extends TestCase
{
    #[TestDox('toString returns correct html meta tag string')]
    public function testToString(): void
    {
        $metaTag = new MetaTag('DC.Title', 'Sample Title');
        $this->assertEquals('<meta name="DC.Title" content="Sample Title">', $metaTag->__toString());
    }
}
