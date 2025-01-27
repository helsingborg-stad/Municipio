<?php

namespace Municipio\StickyPost\Helper;

use PHPUnit\Framework\TestCase;
use Municipio\StickyPost\Config\StickyPostConfig;
use Municipio\StickyPost\Helper\GetStickyOption;
use WpService\Implementations\FakeWpService;

/**
 * Represents a GetStickyOptionTest class.
 */
class GetStickyOptionTest extends TestCase
{
    /**
     * @testdox getOptionKey returns string
     */
    public function testGetOptionKeyReturnsString()
    {
        $stickyPostConfig  = new StickyPostConfig();
        $getOptionInstance = new GetStickyOption(
            $stickyPostConfig,
            new FakeWpService([
                'getOption' => []
            ])
        );

        $key = $getOptionInstance->getOptionKey('test');
        $this->assertEquals($stickyPostConfig->getOptionKeyPrefix() . '_test', $key);
    }

    /**
     * @testdox getOptionKey returns empty array
     */
    public function testGetOptionReturnsEmptyArray()
    {
        $stickyPostConfig  = new StickyPostConfig();
        $getOptionInstance = new GetStickyOption(
            $stickyPostConfig,
            new FakeWpService([
                'getOption' => null
            ])
        );

        $result = $getOptionInstance->getOption('test');
        $this->assertEquals([], $result);
    }

    /**
     * @testdox getOptionKey returns array with sticky posts
     * @runInSeparateProcess
     */
    public function testGetOptionReturnsArrayWithStickyPosts()
    {
        $stickyPostConfig  = new StickyPostConfig();
        $getOptionInstance = new GetStickyOption(
            $stickyPostConfig,
            new FakeWpService([
                'getOption' => ['1' => '1']
            ])
        );

        $result = $getOptionInstance->getOption('test');
        $this->assertEquals(['1' => '1'], $result);
    }
}
