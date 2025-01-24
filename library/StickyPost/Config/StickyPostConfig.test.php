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
     * @testdox getOptionKeyPrefix returns string
     */
    public function testGetOptionKeyPrefixReturnsString()
    {
        $stickyPostConfig = new StickyPostConfig();
        $this->assertIsString($stickyPostConfig->getOptionKeyPrefix());
    }
}
