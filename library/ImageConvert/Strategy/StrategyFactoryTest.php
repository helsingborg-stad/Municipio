<?php

namespace Municipio\ImageConvert\Strategy;

use PHPUnit\Framework\TestCase;
use Municipio\ImageConvert\Strategy\StrategyFactory;
use Municipio\ImageConvert\ConversionCache;
use Municipio\ImageConvert\Config\ImageConvertConfig;
use WpService\Implementations\FakeWpService;

class StrategyFactoryTest extends TestCase
{
    private StrategyFactory $factory;
    private FakeWpService $wpService;
    private ImageConvertConfig $config;
    private ConversionCache $cache;

    protected function setUp(): void
    {
        $this->wpService = new FakeWpService([
            'wpCacheGet' => false,
            'wpCacheSet' => true,
            'wpCacheDelete' => true,
            'addFilter' => true,
            'doAction' => true,
            'isWpError' => false,
            'wpGetImageEditor' => $this->createMockImageEditor(),
            'wpGetAttachmentMetadata' => 1024000,
            'wpAttachmentIs' => true,
            'applyFilters' => function($hook, $value) { return $value; }
        ]);
        
        $this->config = new ImageConvertConfig($this->wpService);
        $this->cache = new ConversionCache($this->wpService);
        $this->factory = new StrategyFactory($this->wpService, $this->config, $this->cache);
    }

    private function createMockImageEditor()
    {
        return new class {
            public function get_size() {
                return ['width' => 1920, 'height' => 1080];
            }
            
            public function resize($width, $height, $crop) {
                return true;
            }
            
            public function save($path) {
                return ['path' => $path, 'file' => basename($path)];
            }
        };
    }

    public function testCreateRuntimeStrategy(): void
    {
        // Test without constant (should default to runtime)
        $strategy = $this->factory->createStrategy();
        $this->assertInstanceOf(RuntimeConversionStrategy::class, $strategy);
        $this->assertEquals('runtime', $strategy->getName());
    }

    public function testCreateBackgroundStrategy(): void
    {
        // Define constant to test background strategy
        if (!defined('MUNICIPIO_IMAGE_CONVERT_STRATEGY')) {
            define('MUNICIPIO_IMAGE_CONVERT_STRATEGY', 'background');
        }
        
        $strategy = $this->factory->createStrategy();
        $this->assertInstanceOf(BackgroundConversionStrategy::class, $strategy);
        $this->assertEquals('background', $strategy->getName());
    }

    public function testGetSupportedStrategies(): void
    {
        $strategies = $this->factory->getSupportedStrategies();
        $this->assertIsArray($strategies);
        $this->assertContains('runtime', $strategies);
        $this->assertContains('background', $strategies);
        $this->assertCount(2, $strategies);
    }

    public function testIsStrategySupported(): void
    {
        $this->assertTrue($this->factory->isStrategySupported('runtime'));
        $this->assertTrue($this->factory->isStrategySupported('background'));
        $this->assertFalse($this->factory->isStrategySupported('invalid'));
    }
}