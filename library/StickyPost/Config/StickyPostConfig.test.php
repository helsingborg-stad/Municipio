<?php

namespace Municipio\StickyPost\Config;

use PHPUnit\Framework\TestCase;

class StickyPostConfigTest extends TestCase
{
    /**
     * @testdox class can be instantiated
     */
    public function testCanBeInstantiated()
    {
        $stickyPostConfig = new StickyPostConfig();
        $this->assertInstanceOf(StickyPostConfig::class, $stickyPostConfig);
    }

    /**
     * @testdox getStickyPostMetaKey returns expected meta key
     */
    public function testGetStickyPostMetaKeyReturnsExpectedMetaKey()
    {
        $stickyPostConfig = new StickyPostConfig();
        $this->assertEquals('sticky-post', $stickyPostConfig->getStickyPostMetaKey());
    }
}